<?php

namespace App\Http\Controllers\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class SocialAuthController extends Controller
{
    public function redirectToProvider()
    {
        return Socialite::driver('twitter')->redirect();
    }


    public function handleProviderCallback()
    {
        $socialUser = Socialite::driver('twitter')->user();

        //仮のメールアドレスを生成
        $tempEmail = $socialUser->getId(). '@temp.example.com'; //mailカラムがrequireのため仮のアドレス登録

        // まず、`twitter_id`で検索し、なければ`email`で検索
        $user = User::where('twitter_id',$socialUser->getId())
        ->orWhere(function($query) use ($socialUser, $tempEmail){
            // email で既存のユーザーを確認（仮のメールアドレスは除外）
            $query->where('email',$socialUser->getEmail() ?? $tempEmail)
            ->whereNull('twitter_id');// twitter_id が NULL の場合に限定
        })->first();


        if (!$user) {

            // アバター画像のダウンロードと保存
            $avatarUrl = $socialUser->getAvatar();
            $avatarContents = file_get_contents($avatarUrl);
            $avatarFilename = 'avatar_' . Str::random(10) . '.jpg'; // ランダムなファイル名を生成
            Storage::disk('public')->put('avatar/' . $avatarFilename, $avatarContents);

            // 新規ユーザー作成
            $user = User::create([
                'twitter_id' => $socialUser->getId(),
                'name' => $socialUser->getName(),
                'email' => $socialUser->getEmail() ?? $socialUser->getId() . '@temp.example.com',
                'email_verified_at' => Carbon::now(), // メール認証済みに設定
                'twitter_token' => $socialUser->token, // OAuth トークン保存
                'twitter_token_secret' => $socialUser->tokenSecret, // OAuth トークンシークレット保存
                'avatar' => $avatarFilename,
            ]);
        } else {
            // 既存ユーザーの情報を更新し、Xの情報を追加
            $user->twitter_id = $socialUser->getId();
            $user->twitter_token = $socialUser->token;
            $user->twitter_token_secret = $socialUser->token_secret;

            if (is_null($user->email_verified_at)) {
                $user->email_verified_at = Carbon::now();
            }

            $user->save();
        }

        Auth::login($user, true);

        return redirect()->route('dashboard');
    }

}
