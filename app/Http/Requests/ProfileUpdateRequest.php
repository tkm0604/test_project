<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'avatar' => ['image','max:1024']
        ];
    }

    public function messages()
{
    return [
        'name.required' => '名前は必須項目です。',
        'name.string' => '名前は文字列で入力してください。',
        'name.max' => '名前は255文字以内で入力してください。',
        'email.required' => 'メールアドレスは必須項目です。',
        'email.lowercase' => 'メールアドレスは小文字で入力してください。',
        'email.email' => 'メールアドレスの形式が正しくありません。',
        'email.max' => 'メールアドレスは255文字以内で入力してください。',
        'email.unique' => 'このメールアドレスは既に登録されています。',
        'avatar.image' => 'アバターには画像ファイルを指定してください。',
        'avatar.max' => 'アバターのサイズは1MB以下である必要があります。',
    ];
}
}
