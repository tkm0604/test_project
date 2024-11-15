<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Comment;
use Illuminate\Support\Facades\Storage;
use Abraham\TwitterOAuth\TwitterOAuth;
use Illuminate\Support\Facades\Log;
use App\Models\Role;
use App\Models\User;
class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // クエリパラメータで並び替えオプションを取得
        $sortOrder = $request->query('sort', 'desc'); // デフォルトは新しい順

        $posts = Post::orderBy('created_at', $sortOrder)->latest()->paginate(10); // 1ページに10件表示
        $user = auth()->user();
        return view('post.index', compact('posts', 'user', 'sortOrder'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('post.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $inputs = $request->validate([
            'title' => 'required|max:255',
            'body' => 'required|max:1000',
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:4096',
        ]);

        // タイトルと本文の合計文字数チェック
        if (mb_strlen($inputs['title'] . $inputs['body']) > 280) {
            return back()->withErrors(['title' => 'タイトルと本文の合計は280文字以内にしてください。'])->withInput();
        }

        $post = new Post();
        $post->title = $request->title;
        $post->body = $request->body;
        $post->user_id = auth()->user()->id;

        $imagePath = null; // 初期化

        if (request('image')) {
            $original = request()->file('image')->getClientOriginalName();
            $name = date('Ymd_His') . '_' . $original;
            request()->file('image')->move('storage/images', $name);
            $post->image = $name;
            $imagePath = storage_path('app/public/images/' . $name);
        }
        if ($post->save()) {
            // 保存が成功した場合
            $message = 'ぼやきが投稿されました';
        } else {
            // 保存が失敗した場合
            $message = 'ぼやき投稿に失敗しました';
        } 

            // Twitterに投稿
            // 正しい画像パスを渡してTwitterに投稿
            $this->postTweet($post->title, $post->body, $imagePath);


        // 成功・失敗メッセージを表示
        return redirect()->route('post.create')->with('message',  $message);
    }




    public function postTweet($title, $body, $imagePath=null)
    {
        $user = auth()->user();

        // 認証情報を取得してTwitterOAuthインスタンスを作成
        $twitter = new TwitterOAuth(
            env('TWITTER_CLIENT_ID'),
            env('TWITTER_CLIENT_SECRET'),
            $user->twitter_token,
           	$user->twitter_token_secret
        );

        // APIバージョンをv1.1に設定
        $twitter->setApiVersion('1.1');

        // 画像がある場合、画像をTwitterへアップロードしてメディアIDを取得
        $mediaId = null;
        if ($imagePath) {
            try {
                // 画像をアップロードしてメディアIDを取得
                $media = $twitter->upload('media/upload', ['media' => $imagePath]);
                $mediaId = $media->media_id_string ?? null;

                if (!$mediaId) {
                    Log::error('メディアIDが取得できませんでした。アップロード結果:' .json_encode($media));
                    return false;
                }
            } catch (\Exception $e) {
                Log::error('Twitterへの画像アップロードに失敗しました: ' . $e->getMessage());
                return false;
            }
        }

        // v2用のTwitterOAuthインスタンスを新しく作成
        $twitterV2 = new TwitterOAuth(
            env('TWITTER_CLIENT_ID'),
            env('TWITTER_CLIENT_SECRET'),
            $user->twitter_token,
            $user->twitter_token_secret
        );

        // v2エンドポイントのツイート内容
        $tweetContent = [
            "text" => $title . "\n" . $body
        ];

        // 画像がある場合、media_idsに追加
        if($mediaId){
            $tweetContent['media'] = [
                'media_ids' => [$mediaId]
            ];
        }


        // Twitterに投稿 (v2 エンドポイント)
            try {
            // v2エンドポイントを指定し、JSONリクエストに設定
            $response = $twitterV2->post('tweets', $tweetContent);

            // レスポンスのHTTPコードを確認
            if ($twitterV2->getLastHttpCode() == 201) {
                Log::info('Twitterへの投稿に成功しました: ' . json_encode($response));
                return true;
            } else {
                Log::error('Twitterへの投稿に失敗しました: ' . json_encode($response));
                return false;
            }
        } catch (\Exception $e) {
            // エラーが発生した場合の処理
            Log::error('例外が発生しました: ' . $e->getMessage());
            return false;
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(Post $post,User $user)
    {
    // カウントをセッションで制御
    $viewedPosts = session()->get('viewed_posts', []);

    // 投稿したユーザー以外、かつ未カウントの場合のみカウント
    if (auth()->id() !== $post->user_id && !in_array($post->id, $viewedPosts)) {
        $post->increment('views');
        session()->push('viewed_posts', $post->id); // カウント済みに設定
    }
    $isAdmin = auth()->check() && auth()->user()->roles->contains('id', 3);

    return view('post.show', [
        'post' => $post,
        'isAdmin' => $isAdmin,
    ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Post $post)
    {

        if ($request->user()->cannot('update', $post)) {
            abort(403);
        }

        return view('post.edit', compact('post'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        if ($request->user()->cannot('update', $post)) {
            abort(403);
        }

        $inputs = $request->validate([
            'title' => 'required|max:255',
            'body' => 'required|max:1000',
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:4096',
        ]);

        // タイトルと本文の合計文字数チェック
        if (mb_strlen($inputs['title'] . $inputs['body']) > 280) {
            return back()->withErrors(['title' => 'タイトルと本文の合計は280文字以内にしてください。'])->withInput();
        }

        $post->title = $request->title;
        $post->body = $request->body;

        // 古い画像の削除と新しい画像の保存
        if (request('image')) {
            //古い画像が存在する場合は削除する
            if ($post->image) {
                $oldImage = 'images/' . $post->image;
                Storage::disk('public')->delete($oldImage);
            }
        }

        // 新しい画像のアップロード
        $original = request()->file('image')->getClientOriginalName();
        $name = date('Ymd_His') . '_' . $original;
        request()->file('image')->move('storage/images', $name);
        $post->image = $name;

        $post->save();
        return redirect()->route('post.show', $post)->with('message', '投稿を編集しました');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Post $post)
    {
        if ($request->user()->cannot('delete', $post)) {
            abort(403);
        }

        // 投稿に紐づく画像がある場合、その画像を削除
        if ($post->image) { // 画像が設定されているかチェック
            $oldImage = 'images/' . $post->image;
            Storage::disk('public')->delete($oldImage); // ストレージから画像を削除
        }

        $post->comments()->delete();
        $post->delete();
        return redirect()->route('post.index')->with('message', '投稿を削除しました');
    }

    /**
     * 自分の投稿だけを取得、表示
     */
    public function mypost()
    {
        $user = auth()->user()->id;
        $posts = Post::where('user_id', $user)->latest()->paginate(10); // 1ページに10件表示
        return view('post.mypost', compact('posts'));
    }

    public function mycomment()
    {
        $user = auth()->user()->id;
        // $comments = Comment::where('user_id', $user)->orderBy('created_at', 'desc')->get();
        $comments = Comment::where('user_id', $user)->latest()->paginate(10); // 1ページに10件表示

        return view('post.mycomment', compact('comments'));
    }
}
