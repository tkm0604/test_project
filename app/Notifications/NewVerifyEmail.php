<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;

class NewVerifyEmail extends VerifyEmail
{
    use Queueable;

    protected function buildMailMessage($url){
        return (new MailMessage)
        ->subject('メールアドレスの確認')
        ->line('ご登録ありがとうございます。')
        ->line('新しいメンバーが増えてとても嬉しいです')
        ->action('このボタンをクリック', $url)
        ->line('上記ボタンをクリックすると、ご登録が完了します。');
    }
}
