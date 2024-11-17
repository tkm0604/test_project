<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class RegisteredUserControllerTest extends TestCase
{

    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // CSRFトークン検証を無効化
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
    }

public function test_it_displays_the_registration_view(): void
{
    //registerにGetリクエストを送信
    $response = $this->get('/register');

    //Httpステータスコード200を返すことを確認
    $response->assertStatus(200);
}

public function test_it_registers_a_new_user(): void
{
    // `public`ディスクをモック
    Storage::fake('public');

    // テストデータの準備
    $data = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'avatar' => UploadedFile::fake()->image('avatar.jpg', 100, 100), // モック画像
    ];

    // POSTリクエストを送信
    $response = $this->post('/register', $data);

    // リダイレクト先を検証
    $response->assertRedirect(route('dashboard'));

    // データベースにユーザーが保存されているか検証
    $this->assertDatabaseHas('users', [
        'email' => 'test@example.com',
    ]);

    // 保存されたファイルを検証
    Storage::disk('public')->assertExists('avatar/' . date('Ymd_His') . '_avatar.jpg');
}

}
