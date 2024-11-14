<?php

namespace App\Http\Controllers\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SocialAuthController extends Controller
{
    public function redirectToProvider()
    {
        return Socialite::driver('twitter')->redirect();
    }


    public function handleProviderCallback()
    {
        $socialUser = Socialite::driver('twitter')->user();

        // `twitter_id`または`email`でユーザーを検索
        $user = User::where('twitter_id', $socialUser->getId())
            ->orWhere('email', $socialUser->getEmail() ?? $socialUser->getId() . '@temp.example.com')
            ->first();

        if (!$user) {
            // 新規ユーザー作成
            $user = User::create([
                'twitter_id' => $socialUser->getId(),
                'name' => $socialUser->getName(),
                'email' => $socialUser->getEmail() ?? $socialUser->getId() . '@temp.example.com',
                'email_verified_at' => Carbon::now(), // メール認証済みに設定
                'twitter_token' => $socialUser->token, // OAuth トークン保存
                'twitter_token_secret' => $socialUser->tokenSecret, // OAuth トークンシークレット保存
            ]);
        } else {
            // 既存ユーザーの場合、twitter_idとメール認証を確実に更新
            $user->twitter_id = $socialUser->getId();
            $user->name = $socialUser->getName();

            if (is_null($user->email_verified_at)) {
                $user->email_verified_at = Carbon::now();
            }

            $user->save();
        }

        Auth::login($user, true);

        return redirect()->route('dashboard');
    }

}
