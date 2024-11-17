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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
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
        $tempEmail = $socialUser->getId() . '@temp.example.com'; //mailカラムがrequireのため仮のアドレス登録

        // 既存ユーザーをtwitter_idカラムから確認
        $existingUser = User::where('twitter_id', $socialUser->getId())->first();

        if ($existingUser) {
            // 既存ユーザーがいる場合、そのユーザーをログイン
            $existingUser->update([
                'twitter_token' => $socialUser->token,
                'twitter_token_secret' => $socialUser->tokenSecret,
            ]);
            Auth::login($existingUser, true);
        } else {
            // アバター画像のダウンロードと保存
            $avatarUrl = $socialUser->getAvatar();
            $response = Http::get($avatarUrl);
            $avatarContents = $response->body(); // レスポンスのボディを取得
            $avatarFilename = 'avatar_' . Str::random(10) . '.jpg'; // ランダムなファイル名を生成
            Storage::disk('public')->put('avatar/' . $avatarFilename, $avatarContents);

            // 新規ユーザー作成
            $user = User::create([
                'twitter_id' => $socialUser->getId(),
                'name' => $socialUser->getName(),
                'email' => $tempEmail,
                'email_verified_at' => Carbon::now(), // メール認証済みに設定
                'twitter_token' => $socialUser->token, // OAuth トークン保存
                'twitter_token_secret' => $socialUser->tokenSecret, // OAuth トークンシークレット保存
                'avatar' => $avatarFilename,
            ]);

            Auth::login($user, true);
        }

        return redirect()->route('dashboard');
    }
}
