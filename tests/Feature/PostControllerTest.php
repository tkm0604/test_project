<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Post;
use App\Models\User;

class PostControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function unauthenticated_users_are_redirected_to_login()
    {
        $response = $this->get('/dashboard');

        // 未認証ユーザーはログインページにリダイレクトされるか確認
        $response->assertRedirect('/login');
    }

    /** @test */
    public function authenticated_users_can_access_dashboard()
    {
        $user = User::factory()->create();

        // 認証済みユーザーでアクセスする
        $response = $this->actingAs($user)->get('/dashboard');

        // ステータスコード200と正しいビューが返されるか確認
        $response->assertStatus(200);
        $response->assertViewIs('post.index');
    }

    /** @test */
    public function posts_are_displayed_in_the_index_view()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create([
            'title' => 'テスト投稿',
            'body' => 'テスト本文',
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        // 投稿がビューに表示されるか確認
        $response->assertSee('テスト投稿');
        $response->assertSee('テスト本文');
    }
}
