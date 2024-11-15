<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function a_user_can_create_a_post()
    {
         // ユーザーを作成
        $user = User::factory()->create();

        // ユーザーがポストを作成
        $post = Post::factory()->create([
            'user_id' => $user->id,
            'title' => 'Test Post',
            'body' => 'This is a test post'
        ]);

        // データベースに投稿が正しく保存されていることを確認
        $this->assertDatabaseHas('posts', [
            'user_id' => $user->id,
            'title' => 'Test Post',
            'body' => 'This is a test post'
        ]);
    }

    /** @test */
    public function a_user_can_update_a_post()
    {
        // ユーザーを作成
        $user = User::factory()->create();

        // ユーザーがポストを作成
        $post = Post::factory()->create([
            'user_id' => $user->id,
            'title' => 'Test Post',
            'body' => 'This is a test post'
        ]);

        // ポストを更新
        $post->update([
            'title' => 'Updated Post',
            'body' => 'This is an updated post'
        ]);

        // データベースに投稿が正しく保存されていることを確認
        $this->assertDatabaseHas('posts', [
            'user_id' => $user->id,
            'title' => 'Updated Post',
            'body' => 'This is an updated post'
        ]);
    }

    /** @test */
    public function a_user_can_delete_their_post()
    {
        // ユーザーを作成
        $user = User::factory()->create();

        // ユーザーがポストを作成
        $post = Post::factory()->create([
            'user_id' => $user->id,
            'title' => 'Test Post',
            'body' => 'This is a test post'
        ]);

        // ポストを削除
        $post->delete();

        // データベースに投稿が削除されていることを確認
        $this->assertDatabaseMissing('posts', [
            'user_id' => $user->id,
            'title' => 'Test Post',
            'body' => 'This is a test post'
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
