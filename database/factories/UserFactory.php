<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'avatar' => $this->faker->imageUrl(100, 100, 'people', true, 'Avatar'),
            'password' => bcrypt('password'),
            'twitter_id' => $this->faker->optional()->numerify('#######'), // ランダム値またはnull
            'twitter_token' => $this->faker->optional()->lexify(Str::random(32)), // ランダム文字列またはnull
            'twitter_token_secret' => $this->faker->optional()->lexify(Str::random(32)), // ランダム文字列またはnull
            'email_verified_at' => now(),
        ];
    }
   // Twitter関連フィールドを空（null）にする状態
   public function withoutTwitter(): static
   {
       return $this->state(function (array $attributes) {
           return [
               'twitter_id' => null,
               'twitter_token' => null,
               'twitter_token_secret' => null,
           ];
       });
   }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
