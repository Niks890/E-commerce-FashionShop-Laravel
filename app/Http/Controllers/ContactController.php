<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\SendEmailJob;


class ContactController extends Controller
{

    public function sendContact(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'email' => 'required',
            'message' => 'required',
        ]);


        // Đẩy job vào hàng đợi
        SendEmailJob::dispatch($data);
        // SendEmailJob::dispatchSync($data);
        //   Mail::to($data['email'])->send(new ContactMail($data));
        // dd($data);
        return view('sites.success.contact');
    }

    public function contactSuccess()
    {
        return view('sites.emails.contact_info');
    }
}
