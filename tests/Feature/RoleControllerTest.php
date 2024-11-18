<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class RoleControllerTest extends TestCase
{

    use RefreshDatabase;
    /**
     *attach()のテスト
     */

    //  正常な役割付与のテスト
    public function test_admin_can_attach_role_to_user()
    {

    // ロールデータの作成
    \DB::table('roles')->insert([
        ['id' => 1, 'name' => 'admin', 'created_at' => now(), 'updated_at' => now()],
        ['id' => 2, 'name' => 'user', 'created_at' => now(), 'updated_at' => now()],
    ]);

    // ユーザーの作成
    $user = User::factory()->create();

    // 管理者ユーザーを作成
    $admin = User::factory()->create();

    // 管理者にロールを割り当て
    \DB::table('role_user')->insert([
        ['role_id' => 1, 'user_id' => $admin->id, 'created_at' => now(), 'updated_at' => now()],
    ]);

    // 管理者として認証
    $this->actingAs($admin);

    // リクエストデータ
    $requestData = [
        'role' => 2, // ユーザーロールを付与
    ];

    // attach メソッドへのリクエストを実行
    $response = $this->patch(route('role.attach', ['user' => $user->id]), $requestData);

    // ステータスコードが 302（リダイレクト）であることを確認
    $response->assertStatus(302);

    // ユーザーにロールが正しく付与されていることを確認
    $this->assertDatabaseHas('role_user', [
        'role_id' => 2,
        'user_id' => $user->id,
    ]);

    }

//通常ユーザーがアクセスできないテスト
public function test_non_admin_user_cannot_attach_role_to_user()
{
    // 通常ユーザーを作成
    $nonAdmin = User::factory()->create();

    // ユーザーの作成
    $user = User::factory()->create();

    // 通常ユーザーとして認証
    $this->actingAs($nonAdmin);

    // リクエストデータ
    $requestData = [
        'role' => 2, // ユーザーロールを付与
    ];

    // attach メソッドへのリクエストを実行
    $response = $this->patch(route('role.attach', ['user' => $user->id]), $requestData);

    // ステータスコードが 403（権限なし）であることを確認
    $response->assertStatus(403);

    // ユーザーにロールが付与されていないことを確認
    $this->assertDatabaseMissing('role_user', [
        'role_id' => 2,
        'user_id' => $user->id,
    ]);
}

//未認証ユーザーがリダイレクトされるテスト
public function test_guest_cannot_attach_role_to_user()
{
    // ユーザーの作成
    $user = User::factory()->create();

    // リクエストデータ
    $requestData = [
        'role' => 2, // ユーザーロールを付与
    ];

    // attach メソッドへのリクエストを実行（未認証状態）
    $response = $this->patch(route('role.attach', ['user' => $user->id]), $requestData);

    // ステータスコードが 302（リダイレクト）であることを確認
    $response->assertStatus(302);

    // ログインページにリダイレクトされることを確認
    $response->assertRedirect(route('login'));

    // ユーザーにロールが付与されていないことを確認
    $this->assertDatabaseMissing('role_user', [
        'role_id' => 2,
        'user_id' => $user->id,
    ]);
}

}
