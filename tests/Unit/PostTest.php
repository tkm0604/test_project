<?php

namespace Tests\Unit;

// use PHPUnit\Framework\TestCase;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_example(): void
    {
        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function it_belongs_to_a_user()
    {
        // テスト用ユーザーを作成
        $user = User::factory()->create();
        // ユーザーに紐づいたPostを作成
        $post = Post::factory()->create(['user_id'=>$user->id]);
        // Postのuser()メソッドがUserインスタンスを返すか確認
        $this->assertInstanceOf(User::class,$post->user);
    }
}
