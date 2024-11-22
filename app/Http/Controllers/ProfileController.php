<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Models\Role;

class ProfileController extends Controller
{

    public function index()
    {
        $users = User::all();
        return view('profile.index', compact('users'));
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $roles = Role::all();
        return view('profile.edit', [
            'user' => $request->user(),
            'roles' => $roles
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {

        //ダミーユーザーを特定して編集を制限
        if(auth()->user()->email === 'test01@test.com'){
            return redirect()->back()->with('error_profile','test01ユーザーはプロフィールの編集はできません');
        }

        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        // アバター画像の保存
        if ($request->validated('avatar')) {
            // 古いアバター画像の削除
            $user = $request->user();
            if ($user->avatar !== 'user_default.jpg') {
                $oldavatar = 'avatar/' . $user->avatar;
                Storage::disk('public')->delete($oldavatar);
            }

            // 新しいアバター画像の保存
            $avatarPath = $request->file('avatar')->store('avatar', 'public');
            $user->avatar = basename($avatarPath);
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }


    public function adedit(User $user)
    {
        $admin = true;
        $roles = Role::all();

        return view('profile.edit', [
            'user' => $user,
            'admin' => $admin,
            'roles' => $roles,
        ]);
    }

    public function adupdate(User $user, Request $request): RedirectResponse
    {
        $inputs = $request->validate([
            'name' => ['string', 'max:255'],
            'email' => ['email', 'max:255', Rule::unique(User::class)->ignore($user)],
            'avatar' => ['image', 'max:1024'],
        ]);

        // アバター画像の保存
        if ($request->hasFile('avatar')) {
            // 古いアバターの削除
            if ($user->avatar !== 'user_default.jpg') {
                $oldavatar = 'avatar/' . $user->avatar;
                Storage::disk('public')->delete($oldavatar);
            }

            // 新しいアバターの保存
            $avatarPath = $request->file('avatar')->store('avatar', 'public');
            $user->avatar = basename($avatarPath);
        }

        $user->name = $inputs['name'];
        $user->email = $inputs['email'];
        $user->save();

        return Redirect::route('profile.adedit', compact('user'))->with('status', 'profile-updated');
    }

    public function addestroy(User $user)
    {
        if ($user->avatar !== 'user_default.jpg') {
            $oldavatar = 'avatar/' . $user->avatar; // 'avatar/test_avatar.jpg'
            Storage::disk('public')->delete($oldavatar); // storage/app/public/avatar/test_avatar.jpg を削除
        }
        $user->roles()->detach();
        $user->delete();
        return back()->with('message', 'ユーザーを削除しました');
    }

    //userによる自身のアカウント削除
    public function destroy(Request $request):RedirectResponse
    {

        $user = $request->user();

        //ダミーユーザーを特定して変更を制限
        if($user->email === 'test01@test.com'){
            return redirect()->back()->with('error_delete','test01ユーザーは削除できません');
        }
        
        //ログアウト処理
        Auth::logout();

        //アバター画像がデフォルトでない場合、削除
        if($user->avatar !=='user_default.jpg')
        {
            $oldavatar = 'avatar/'.$user->avatar;
            Storage::disk('public')->delete($oldavatar);
        }

        //ユーザーアカウントを削除
        $user->delete();

        //セッション無効化
        $request->session()->invalidate();
        //CSRFトークンやセッションIDを再生成することで、古いトークンやセッションを無効化し、再利用を防止
        $request->session()->regenerateToken();

        //ホームページにリダイレクト
        return Redirect::to('/')->with('status','アカウントを削除しました。');
    }
}
