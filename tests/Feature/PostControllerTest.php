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

    /** @test */
    public function unauthenticated_user_is_redirected_from_edit_page()
    {
         // テスト用の投稿データを作成
         $post = \App\Models\Post::factory()->create();

         // 未認証の状態で編集ページにアクセス
         $response = $this->get(route('post.edit',$post->id));

         // ログインページへのリダイレクトを確認
         $response->assertRedirect(route('login'));
    }


    /** @test */
    public function authenticated_user_can_update_post_with_valid_data()
    {
        // テスト用のユーザーと投稿を作成
        $user = \App\Models\User::factory()->create();
        $post = \App\Models\Post::factory()->create(['user_id' => $user->id]);

        // 認証済みユーザーとしてリクエストを送信
        $response = $this->actingAs($user)->put(route('post.update', $post->id), [
            'title' => 'Update Title',
            'body'  => 'Update Body',
        ]);

        // セッションにバリデーションエラーがないことを確認
        $response->assertSessionHasNoErrors();

        // 更新後のデータをリフレッシュして確認
        $post->refresh();

        // データが更新されているか確認
        $this->assertEquals('Update Title', $post->title);
        $this->assertEquals('Update Body', $post->body);

        // 投稿詳細ページへのリダイレクトとセッションメッセージの確認
        $response->assertRedirect(route('post.show', $post->id));
        $response->assertSessionHas('message', '投稿を編集しました');
    }


    /** @test */
    public function update_requires_valid_data()
    {
        $user = \App\Models\User::factory()->create();
        $post = \App\Models\Post::factory()->create([
            'user_id'=> $user->id,
        ]);
        // 認証済みユーザーとして無効なデータでリクエストを送信
        $response = $this->actingAs($user)->put(route('post.update',$post->id),[
            'title' => '',
            'body' =>'',
            'image'=>'not-an-image',
        ]);
        // バリデーションエラーを確認
        $response->assertSessionHasErrors(['title','body','image']);
    }
}
