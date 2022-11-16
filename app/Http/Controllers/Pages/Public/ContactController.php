<?php

namespace App\Http\Controllers\Pages\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class ContactController extends Controller
{
    public function index()
    {
        return Inertia::render('Public/Contact');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'sometimes|nullable|string|min:1|max:500',
            'email' => 'sometimes|nullable|email|min:1|max:500',
            'subject' => 'required|string|min:1|max:500',
            'content' => 'required|string|min:1|max:20000'
        ]);

        Mail::raw(
            sprintf('From: %s \r\n Email: %s \r\n %s', $request->input('name', 'None given'), $request->input('email', 'None given'), $request->input('content')),
            function($message) use ($request) {
                $message->to('gpsvault@gmail.com')
                    ->subject($request->input('subject'));
            }
        );

        return redirect()->route('contact');
    }
}
