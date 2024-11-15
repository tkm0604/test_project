<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_be_created()
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'test@example.com'
        ]);
        
        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'test@example.com'
        ]);
    }

  /** @test */
     public function a_user_can_be_updated()
     {
         $user = User::factory()->create([
             'name' => 'Test User',
             'email' => 'test@example.com'
         ]);

         $user->update([
             'name' => 'Jane Doe',
             'email' => 'test2@example.com'
         ]);

         $this->assertDatabaseHas('users', [
             'name' => 'Jane Doe',
             'email' => 'test2@example.com'
         ]);
     }
    /**
     * A basic feature test example.
     */
    // public function test_example(): void
    // {
    //     $response = $this->get('/');

    //     $response->assertStatus(200);
    // }
}
