<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence, // ランダムなタイトル
            'body' => $this->faker->paragraph, // ランダムな本文
            'user_id' => \App\Models\User::factory(), // 関連するユーザーを生成
            'image' => $this->faker->imageUrl(640, 480, 'nature', true, 'Image'), // ランダムな画像URL
        ];
    }
}
