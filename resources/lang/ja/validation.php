<?php


return [
    'required' => ':attribute は必須項目です。',
    'max' => [
        'string' => ':attribute は :max 文字以内で入力してください。',
    ],
    'image' => ':attribute は画像ファイルである必要があります。',

    'attributes'=>[
        'title'=>'件名',
        'body'=>'本文',
        'image'=>'画像'
    ]
];
