<?php


return [
    'required' => ':attribute は必須項目です。',
    'max' => [
        'string' => ':attribute は :max 文字以内で入力してください。',
        'file' => ':attribute は :max KB 以下のファイルを選択してください。',
    ],
    'image' => ':attribute は 2MB 以下の画像ファイルである必要があります。',
    'mimes' => ':attribute は :values の形式である必要があります。',
    'email' => ':attribute は有効なメールアドレス形式である必要があります。',

    'attributes' => [
        'title' => '件名',
        'body' => '本文',
        'image' => '画像',
        'email' => 'メールアドレス',
    ]
];
