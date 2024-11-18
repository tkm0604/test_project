<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use App\Models\Role;
class ProfileControllerTest extends TestCase
{

    use RefreshDatabase;


//--------------------------------------------
// index()のテスト
//--------------------------------------------
    public function test_admin_can_access_profile_index()
    {
        // role テーブルに id=1, id=2 のデータを作成
        \DB::table('roles')->insert([
            ['id' => 1, 'name' => 'admin', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'user', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 管理者ユーザーを作成（id=1に設定）
        $admin = User::factory()->create(['id' => 1]);

        // roleusers テーブルにデータを作成
        \DB::table('role_user')->insert([
            ['role_id' => 1, 'user_id' => $admin->id, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 管理者として認証
        $this->actingAs($admin);

        // profile.index にアクセス
        $response = $this->get(route('profile.index'));

        // ステータスコードが 200 であることを確認
        $response->assertStatus(200);

        // 正しいビューが表示されることを確認
        $response->assertViewIs('profile.index');
    }


    public function test_non_admin_users_cannot_access_profile_index()
    {
        // 通常ユーザーを作成（idが1以外）
        $user = User::factory()->create(['id' => 2]);

        // 通常ユーザーとして認証
        $this->actingAs($user);

        // profile.index にアクセス
        $response = $this->get(route('profile.index'));

        // ステータスコードが 403 であることを確認
        $response->assertStatus(403);
    }

    public function test_unauthenticated_users_are_redirected_to_login()
    {
        // 未認証状態で profile.index にアクセス
        $response = $this->get(route('profile.index'));

        // ステータスコードが 302（リダイレクト）であることを確認
        $response->assertStatus(302);

        // /login にリダイレクトされることを確認
        $response->assertRedirect(route('login'));
    }



//--------------------------------------------
// edit()のテスト
//--------------------------------------------
public function test_authenticated_user_can_access_edit()
{
    // ロールデータの作成
    \DB::table('roles')->insert([
        ['id' => 1, 'name' => 'admin', 'created_at' => now(), 'updated_at' => now()],
        ['id' => 2, 'name' => 'user', 'created_at' => now(), 'updated_at' => now()],
    ]);

    // 通常ユーザーを作成
    $user = User::factory()->create();

    // 通常ユーザーとして認証
    $this->actingAs($user);

    // profile.edit にアクセス
    $response = $this->get(route('profile.edit'));

    // ステータスコードが 200 であることを確認
    $response->assertStatus(200);

    // 正しいビューが表示されることを確認
    $response->assertViewIs('profile.edit');

    // ビューに渡されるデータが正しいことを確認
    $response->assertViewHas('user', $user);
    $response->assertViewHas('roles', Role::all());
}

public function test_unauthenticated_user_is_redirected_to_login_when_accessing_edit()
{
    // 未認証状態で profile.edit にアクセス
    $response = $this->get(route('profile.edit'));

    // ステータスコードが 302（リダイレクト）であることを確認
    $response->assertStatus(302);

    // /login にリダイレクトされることを確認
    $response->assertRedirect(route('login'));
}



//--------------------------------------------
// update()のテスト
//--------------------------------------------

//アバター画像付きで更新する場合
public function test_authenticated_user_can_update_profile_with_avatar()
{
    // テスト用にストレージをモック
    Storage::fake('public');

    // 認証済みユーザーを作成
    $user = User::factory()->create(['email_verified_at' => now(), 'avatar' => 'user_default.jpg']);

    // ダミー画像をアップロード
    $avatar = \Illuminate\Http\UploadedFile::fake()->image('avatar.jpg');

    // 更新データ
    $updateData = [
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
        'avatar' => $avatar,
    ];

    // ユーザーとして認証し、プロフィールを更新
    $response = $this->actingAs($user)->patch(route('profile.update'), $updateData);

    // ステータスコードが 302（リダイレクト）であることを確認
    $response->assertStatus(302);

    // プロフィールページにリダイレクトされることを確認
    $response->assertRedirect(route('profile.edit'));

    // データベースが更新されていることを確認
    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
    ]);

    // 新しいアバター画像がストレージに保存されていることを確認
    $avatarPath = 'avatar/' . $user->fresh()->avatar;
    Storage::disk('public')->assertExists($avatarPath);

    // 古いアバター画像が削除されていることを確認
    Storage::disk('public')->assertMissing('avatar/user_default.jpg');
}


//--------------------------------------------
// adedit()のテスト
//--------------------------------------------

// 管理者として認証し、adedit にアクセス
public function test_admin_can_access_adedit()
{
    // ロールデータの作成
    \DB::table('roles')->insert([
        ['id' => 1, 'name' => 'admin', 'created_at' => now(), 'updated_at' => now()],
        ['id' => 2, 'name' => 'user', 'created_at' => now(), 'updated_at' => now()],
    ]);

    // 管理者ユーザーを作成
    $admin = User::factory()->create();

    // 管理者にロールを割り当て
    \DB::table('role_user')->insert([
        ['role_id' => 1, 'user_id' => $admin->id, 'created_at' => now(), 'updated_at' => now()],
    ]);

    // 編集対象の通常ユーザーを作成
    $userToEdit = User::factory()->create();

    // 管理者として認証
    $this->actingAs($admin);

    // adedit メソッドへの GET リクエストを実行
    $response = $this->get(route('profile.adedit', $userToEdit->id));

    // ステータスコードが 200 であることを確認
    $response->assertStatus(200);

    // 正しいビューが使用されていることを確認
    $response->assertViewIs('profile.edit');

    // ビューに渡されるデータを確認
    $response->assertViewHas('user', $userToEdit);
    $response->assertViewHas('admin', true);
    $response->assertViewHas('roles', Role::all());
}



//通常ユーザーがアクセス不可の場合のテスト
public function test_non_admin_user_cannot_access_adedit()
{
    // 通常ユーザーを作成
    $user = User::factory()->create();

    // 編集対象の通常ユーザーを作成
    $userToEdit = User::factory()->create();

    // 通常ユーザーとして認証
    $this->actingAs($user);

    // adedit メソッドへの GET リクエストを実行
    $response = $this->get(route('profile.adedit', $userToEdit->id));

    // ステータスコードが 403 であることを確認
    $response->assertStatus(403);
}

//--------------------------------------------
// adupdate()のテスト
//--------------------------------------------

//有効なデータで成功するテスト
public function test_admin_can_update_user_profile()
{
    // テスト用にストレージをモック
    Storage::fake('public');

    // ロールデータの作成
    \DB::table('roles')->insert([
        ['id' => 1, 'name' => 'admin', 'created_at' => now(), 'updated_at' => now()],
    ]);

    // 管理者ユーザーを作成
    $admin = User::factory()->create();

    // 管理者にロールを割り当て
    \DB::table('role_user')->insert([
        ['role_id' => 1, 'user_id' => $admin->id, 'created_at' => now(), 'updated_at' => now()],
    ]);

    // 編集対象のユーザーを作成
    $userToEdit = User::factory()->create(['avatar' => 'user_default.jpg']);

    // 新しいアバター画像を準備
    $newAvatar = \Illuminate\Http\UploadedFile::fake()->image('new_avatar.jpg');

    // 更新データ
    $updateData = [
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
        'avatar' => $newAvatar,
    ];

    // 管理者として認証し、更新リクエストを送信
    $response = $this->actingAs($admin)->patch(route('profile.adupdate', $userToEdit->id), $updateData);

    // ステータスコードが 302（リダイレクト）であることを確認
    $response->assertStatus(302);

    // リダイレクト先が正しいことを確認
    $response->assertRedirect(route('profile.adedit', ['user' => $userToEdit->id]));

    // データベースが更新されていることを確認
    $this->assertDatabaseHas('users', [
        'id' => $userToEdit->id,
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
    ]);

    // 新しいアバター画像が保存されていることを確認
    Storage::disk('public')->assertExists('avatar/' . $userToEdit->fresh()->avatar);

    // 古いアバター画像が削除されていることを確認
    Storage::disk('public')->assertMissing('avatar/user_default.jpg');
}

//無効なデータで失敗するテスト
public function test_admin_cannot_update_user_profile_with_invalid_data()
{
    // ロールデータの作成
    \DB::table('roles')->insert([
        ['id' => 1, 'name' => 'admin', 'created_at' => now(), 'updated_at' => now()],
    ]);

    // 管理者ユーザーを作成
    $admin = User::factory()->create();

    // 管理者にロールを割り当て
    \DB::table('role_user')->insert([
        ['role_id' => 1, 'user_id' => $admin->id, 'created_at' => now(), 'updated_at' => now()],
    ]);

    // 編集対象のユーザーを作成
    $userToEdit = User::factory()->create();

    // 無効な更新データ
    $invalidData = [
        'name' => '', // 空の名前
        'email' => 'invalid-email', // 無効なメールアドレス
        'avatar' => 'not-an-image', // 画像ではないデータ
    ];

    // 管理者として認証し、更新リクエストを送信
    $response = $this->actingAs($admin)->patch(route('profile.adupdate', $userToEdit->id), $invalidData);

    // ステータスコードが 302（リダイレクト）であることを確認
    $response->assertStatus(302);

    // セッションにバリデーションエラーが含まれていることを確認
    $response->assertSessionHasErrors(['name', 'email', 'avatar']);

    // データベースが更新されていないことを確認
    $this->assertDatabaseHas('users', [
        'id' => $userToEdit->id,
        'name' => $userToEdit->name,
        'email' => $userToEdit->email,
    ]);
}

//--------------------------------------------
// addestroy()のテスト
//--------------------------------------------

//成功時のテスト
public function test_admin_can_delete_user()
{
    // テスト用にストレージをモック
    Storage::fake('public');

    // ロールデータの作成
    \DB::table('roles')->insert([
        ['id' => 1, 'name' => 'admin', 'created_at' => now(), 'updated_at' => now()],
    ]);

    // 管理者ユーザーを作成
    $admin = User::factory()->create();

    // 管理者にロールを割り当て
    \DB::table('role_user')->insert([
        ['role_id' => 1, 'user_id' => $admin->id, 'created_at' => now(), 'updated_at' => now()],
    ]);

    // 削除対象のユーザーを作成
    $userToDelete = User::factory()->create(['avatar' => 'test_avatar.jpg']);

    // モックストレージにアバターを追加
    Storage::disk('public')->put('avatar/test_avatar.jpg', 'dummy content');

    // 管理者として認証
    $this->actingAs($admin);

    // addestroy メソッドへの DELETE リクエストを実行
    $response = $this->delete(route('profile.addestroy', $userToDelete->id));

    // ステータスコードが 302（リダイレクト）であることを確認
    $response->assertStatus(302);

    // メッセージがセッションに保存されていることを確認
    $response->assertSessionHas('message', 'ユーザーを削除しました');

    // 該当ユーザーがデータベースから削除されていることを確認
    $this->assertDatabaseMissing('users', ['id' => $userToDelete->id]);

    // 該当ユーザーのアバターがストレージから削除されていることを確認
    Storage::disk('public')->assertMissing('avatar/test_avatar.jpg');
}

//アバターがデフォルトの場合のテスト
public function test_admin_can_delete_user_with_default_avatar()
{
    // ロールデータの作成
    \DB::table('roles')->insert([
        ['id' => 1, 'name' => 'admin', 'created_at' => now(), 'updated_at' => now()],
    ]);

    // 管理者ユーザーを作成
    $admin = User::factory()->create();

    // 管理者にロールを割り当て
    \DB::table('role_user')->insert([
        ['role_id' => 1, 'user_id' => $admin->id, 'created_at' => now(), 'updated_at' => now()],
    ]);

    // デフォルトアバターのユーザーを作成
    $userToDelete = User::factory()->create(['avatar' => 'user_default.jpg']);

    // 管理者として認証
    $this->actingAs($admin);

    // addestroy メソッドへの DELETE リクエストを実行
    $response = $this->delete(route('profile.addestroy', $userToDelete->id));

    // ステータスコードが 302（リダイレクト）であることを確認
    $response->assertStatus(302);

    // メッセージがセッションに保存されていることを確認
    $response->assertSessionHas('message', 'ユーザーを削除しました');

    // 該当ユーザーがデータベースから削除されていることを確認
    $this->assertDatabaseMissing('users', ['id' => $userToDelete->id]);
}

//通常ユーザーがアクセスできないテスト
public function test_non_admin_user_cannot_delete_user()
{
    // 通常ユーザーを作成
    $user = User::factory()->create();

    // 削除対象のユーザーを作成
    $userToDelete = User::factory()->create();

    // 通常ユーザーとして認証
    $this->actingAs($user);

    // addestroy メソッドへの DELETE リクエストを実行
    $response = $this->delete(route('profile.addestroy', $userToDelete->id));

    // ステータスコードが 403（権限なし）であることを確認
    $response->assertStatus(403);
}

//未認証ユーザーがリダイレクトされるテスト
public function test_unauthenticated_user_is_redirected_to_login_when_deleting_user()
{
    // 削除対象のユーザーを作成
    $userToDelete = User::factory()->create();

    // 未認証状態で addestroy メソッドにアクセス
    $response = $this->delete(route('profile.addestroy', $userToDelete->id));

    // ステータスコードが 302（リダイレクト）であることを確認
    $response->assertStatus(302);

    // /login にリダイレクトされることを確認
    $response->assertRedirect(route('login'));
}

}
