<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Session;

class RegisterController extends Controller
{
    public function register()
    {
        return view('register');
    }
    
    public function actionregister(Request $request)
    {
        $users = User::get('username');
        $email = User::get('email');
        $getUsername = User::whereNotIn('username', $users)->get();
        $getEmail = User::whereNotIn('email', $email)->get();


        if($getUsername)
        {
            if(filter_var($getEmail, FILTER_VALIDATE_EMAIL))
            {
                Session::flash('message_failed', 'Register gagal. email tidak terdaftar.');  
            }  
            else
            {
                $user = User::create([
                    'email' => $request->email,
                    'username' => $request->username,
                    'password' => Hash::make($request->password),
                ]);
                
                Session::flash('message_success', 'Register Berhasil. Akun Anda sudah Aktif silahkan Login menggunakan username dan password.');
            }
                
        }
        else
        {
            Session::flash('message_failed', 'Register gagal. username sudah digunakan sebelumnya.');  
        }
        
        return redirect('register');
    }
}
