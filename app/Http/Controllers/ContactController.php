<?php

namespace App\Http\Controllers;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactForm;


class ContactController extends Controller
{
    public function create(){
        return view('contact.create');
    }

    public function store(Request $request){
        $inputs=request()->validate([
            'title'=>'required|max:255',
            'email'=>'required|email|max:255',
            'body'=>'required|max:1000',
        ]);
        Contact::create($inputs);

        // config/mail.phpの中のadmin宛にメールを送信する処理
        Mail::to(config('mail.admin'))->send(new ContactForm($inputs));

        // フォームの中のemail宛にメールを送信する処理
        Mail::to($inputs['email'])->send(new ContactForm($inputs));

        return back()->with('message','メールを送信したのでご確認ください');
    }
}
