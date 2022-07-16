<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ForgotPasswordController extends BaseController
{
    //
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:businesses',
        ]);

        $password = Str::random(8);

        $business = Business::where('email', $request->email)->first();
        $user = $business->user;

        $user->update(['password' => Hash::make($password)]);        

        // Mail::send('password-reset', ['password' => $password, 'username' => $user->full_name], function($message) use($business){
        //     // $message->to($business->email);
        //     $message->to('steve.wandie90@gmail.com');
        //     $message->subject('Reset Password Notification');
        // });

        return $this->sendResponse([$password], "New password has been emailed to you");
    }
}
