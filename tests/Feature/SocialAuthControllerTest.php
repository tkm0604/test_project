<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use  Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Mockery;
class SocialAuthControllerTest extends TestCase
{
    // use RefreshDatabase;

    public function testRedirectToProvider(): void
    {
        // Socialiteをモック
        Socialite::shouldReceive('driver')
            ->with('twitter') // driver('twitter') をモック
            ->once()
            ->andReturnSelf(); // インスタンスを返す

        Socialite::shouldReceive('driver->redirect')
            ->once()
            ->andReturn(redirect('https://twitter.com/oauth/authorize')); // モックでリダイレクトURLを返す

        // Twitterリダイレクトをテスト
        $response = $this->get('/login/x');

        // リダイレクトが正しく行われるかを確認
        $response->assertRedirect('https://twitter.com/oauth/authorize');
    }




    public function testHandleProviderCallback(): void
    {

        $this->withoutExceptionHandling(); // 例外のキャッチを無効化

        // モックするTwitterユーザーデータ
        $mockSocialUser = Mockery::mock('Laravel\Socialite\One\User'); // 正しいクラスをモック
        $mockSocialUser->shouldReceive('getId')->andReturn('1485564101061652481');
        $mockSocialUser->shouldReceive('getName')->andReturn('tkm0604');
        $mockSocialUser->shouldReceive('getEmail')->andReturn(null); // 仮メールが生成される
        $mockSocialUser->shouldReceive('getAvatar')->andReturn('https://example.com/avatar.jpg');
        $mockSocialUser->shouldReceive('token')->andReturn('mock_token');
        $mockSocialUser->shouldReceive('tokenSecret')->andReturn('mock_token_secret');


        // Socialiteをモック
         Socialite::shouldReceive('driver->user')
            ->once()
            ->andReturn($mockSocialUser);

        // HTTPリクエストモック (アバター画像をダウンロード)
         Http::fake([
            'https://example.com/avatar.jpg' => Http::response('mock-avatar-contents', 200),
        ]);

        // モックされたレスポンスを確認
         Http::get('https://example.com/avatar.jpg');

        // ストレージをモック
        Storage::fake('public');

        // 実行
       $this->get('/login/x/callback');

        // 期待するレコードが挿入されているか確認
        $this->assertDatabaseHas('users', [
            'twitter_id' => '1485564101061652481',
            'name' => 'tkm0604',
            'email' => '1485564101061652481@temp.example.com',
        ]);

    }


    /**
     * A basic feature test example.
     */
    // public function test_example(): void
    // {
    //     $response = $this->get('/');

    //     $response->assertStatus(200);
    // }
}
