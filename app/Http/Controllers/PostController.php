<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Comment;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts=Post::orderBy('created_at','desc')->get();
        $user=auth()->user();
        return view('post.index',compact('posts','user'));
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
        $inputs=$request->validate([
            'title'=>'required|max:255',
            'body'=>'required|max:1000',
            'image'=>'image|max:1024',
        ]);
        $post=new Post();
        $post->title=$request->title;
        $post->body=$request->body;
        $post->user_id=auth()->user()->id;
        if(request('image')){
            $original = request()->file('image')->getClientOriginalName();
            $name = date('Ymd_His').'_'.$original;
            request()->file('image')->move('storage/images',$name);
            $post->image = $name;
        }
        $post->save();
        return redirect()->route('post.create')->with('message','投稿を作成しました');
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        return view('post.show',compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request,Post $post)
    {

        if($request->user()->cannot('update',$post)){
            abort(403);
        }

        return view('post.edit',compact('post'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        if ($request->user()->cannot('update', $post)) {
            abort(403);
        }

        $inputs=$request->validate([
            'title'=>'required|max:255',
            'body'=>'required|max:1000',
            'image'=>'image|max:1024'
        ]);

        $post->title=$request->title;
        $post->body=$request->body;

        // 古い画像の削除と新しい画像の保存
        if(request('image')){
            //古い画像が存在する場合は削除する
            if($post->image){
                $oldImage = 'images/'.$post->image;
                Storage::disk('public')->delete($oldImage);
            }
        }

        // 新しい画像のアップロード
        $original=request()->file('image')->getClientOriginalName();
        $name=date('Ymd_His').'_'.$original;
        request()->file('image')->move('storage/images', $name);
        $post->image=$name;

        $post->save();
        return redirect()->route('post.show',$post)->with('message','投稿を編集しました');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request,Post $post)
    {
        if($request->user()->cannot('delete',$post)){
            abort(403);
        }

    // 投稿に紐づく画像がある場合、その画像を削除
    if ($post->image) { // 画像が設定されているかチェック
        $oldImage = 'images/' . $post->image;
        Storage::disk('public')->delete($oldImage); // ストレージから画像を削除
    }

        $post->comments()->delete();
        $post->delete();
        return redirect()->route('post.index')->with('message','投稿を削除しました');
    }

     /**
     * 自分の投稿だけを取得、表示
     */
    public function mypost(){
        $user=auth()->user()->id;
        $posts=Post::where('user_id', $user)->orderBy('created_at', 'desc')->get();
        return view('post.mypost', compact('posts'));
    }

    public function mycomment(){
        $user=auth()->user()->id;
        $comments=Comment::where('user_id',$user)->orderBy('created_at','desc')->get();
        return view('post.mycomment', compact('comments'));
    }

}
