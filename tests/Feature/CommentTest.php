<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Comment;
use App\Models\User;
use App\Models\Post;

class CommentTest extends TestCase
{

    /** @test */
    public function a_user_can_create_a_comment()
    {
        // ユーザーを作成
        $user = User::factory()->create();
        // 投稿を作成
        $post = Post::factory()->create();

        // ユーザーがコメントを作成
        $comment = Comment::factory()->create([
            'user_id' => $user->id,
            'post_id' => $post->id,
            'body' => 'This is a test comment'
        ]);

        // データベースにコメントが正しく保存されていることを確認
        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'post_id' => $post->id,
            'body' => 'This is a test comment'
        ]);
    }

    /** @test */
    public function a_user_can_update_their_comment()
    {
        // ユーザーを作成
        $user = User::factory()->create();
        // 投稿を作成
        $post = Post::factory()->create();

        // ユーザーがコメントを作成
        $comment = Comment::factory()->create([
            'user_id' => $user->id,
            'post_id' => $post->id,
            'body' => 'This is a old comment'
        ]);

        // コメントを更新
        $comment->update([
            'body' => 'This is an new comment'
        ]);

        // データベースにコメントが正しく保存されていることを確認
        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'post_id' => $post->id,
            'body' =>  'This is an new comment'
        ]);
    }

    /** @test */
    public function a_user_can_delete_their_comment()
    {
        // ユーザーを作成
        $user = User::factory()->create();
        // 投稿を作成
        $post = Post::factory()->create();

        // ユーザーがコメントを作成
        $comment = Comment::factory()->create([
            'user_id' => $user->id,
            'post_id' => $post->id,
            'body' => 'This is a test comment'
        ]);

        // コメントを削除
        $comment->delete();

        // データベースにコメントが削除されていることを確認
        $this->assertDatabaseMissing('comments', [
            'user_id' => $user->id,
            'post_id' => $post->id,
            'body' => 'This is a test comment'
        ]);
    }

    //コメントとポストの関連テスト
    /** @test */
    public function a_comment_belongs_to_a_post()
    {
        // ユーザーを作成
        $user = User::factory()->create();
        // 投稿を作成
        $post = Post::factory()->create();

        // ユーザーがコメントを作成
        $comment = Comment::factory()->create([
            'user_id' => $user->id,
            'post_id' => $post->id,
            'body' => 'This is a test comment'
        ]);

        // コメントが正しく投稿に関連付けられていることを確認
        $this->assertEquals($post->id, $comment->post->id);
    }

    //コメントとユーザーの関連テスト
    /** @test */
    public function a_comment_belongs_to_a_user()
    {
        // ユーザーを作成
        $user = User::factory()->create();
        // 投稿を作成
        $post = Post::factory()->create();

        // ユーザーがコメントを作成
        $comment = Comment::factory()->create([
            'user_id' => $user->id,
            'post_id' => $post->id,
            'body' => 'This is a test comment'
        ]);

        // コメントが正しくユーザーに関連付けられていることを確認
        $this->assertEquals($user->id, $comment->user->id);
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
