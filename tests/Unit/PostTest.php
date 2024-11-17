<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function title_is_required()
    {
        // テスト用ユーザーの認証
        $this->actingAs(User::factory()->create());

        // タイトルが空で投稿を作成する（エラーを期待）
        $response = $this->post('/post', [
            'title' => '',
            'body' => 'Valid body content.',
        ]);

        $response->assertSessionHasErrors('title'); // タイトルに関するエラーがあることを確認
    }

    /** @test */
    public function body_is_required()
    {
        // テスト用ユーザーの認証
        $this->actingAs(User::factory()->create());

        // 本文が空で投稿を作成する（エラーを期待）
        $response = $this->post('/post', [
            'title' => 'Valid Title',
            'body' => '',
        ]);

        $response->assertSessionHasErrors('body'); // 本文に関するエラーがあることを確認
    }

    /** @test */
    public function valid_post_can_be_created()
    {
        // テスト用ユーザーの認証
        $this->actingAs(User::factory()->create());

        // 正しいデータで投稿を作成する
        $response = $this->post('/post', [
            'title' => 'Valid Title',
            'body' => 'Valid body content.',
        ]);

        $response->assertSessionHasNoErrors(); // エラーがないことを確認
        $response->assertRedirect(route('post.create')); // リダイレクトの確認
    }
}
