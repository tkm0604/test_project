<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Contact;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactForm;

class ContactControllerTest extends TestCase
{

    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     */

    public function test_contactCreate(): void
    {
        $response = $this->get('/contact/create');
        $response->assertStatus(200);
        $response->assertViewIs('contact.create');
    }

    public function test_contactStore(): void
    {
        Mail::fake();
        $data = [
            'title' => 'test title',
            'email' => 'test@example.com',
            'body' => 'This is a test message.',
        ];

        $response = $this->post('/contact/store', $data);
        // データベースに保存されていることを確認
        $this->assertDatabaseHas('contacts', $data);
        // メールが送信されていることを確認
        Mail::assertSent(ContactForm::class, 2);
        // フラッシュメッセージが設定されていることを確認
        $response->assertSessionHas('message', 'メールを送信したのでご確認ください');
        // リダイレクトされることを確認
        $response->assertRedirect();
    }

}
