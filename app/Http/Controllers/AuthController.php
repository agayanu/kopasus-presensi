<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }

    public function showFormLogin()
    {
        if (Auth::check()) { 
            return redirect()->route('home');
        }
        return view('login');
    }

    public function login(Request $request)
    {
        $rules = [
            'username' => 'required|string',
            'password' => 'required|string'
        ];
  
        $messages = [
            'username.required' => 'Username wajib diisi',
            'username.string'   => 'Username harus berupa string',
            'password.required' => 'Password wajib diisi',
            'password.string'   => 'Password harus berupa string'
        ];
  
        $validator = Validator::make($request->all(), $rules, $messages);
  
        if($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput($request->all);
        }
  
        $user = $request->input('username');
        $pass = $request->input('password');

        $data = [
            'username' => $user,
            'password' => $pass,
        ];
  
        Auth::attempt($data, $request->get('remember'));
  
        if(Auth::check()) {
            return redirect()->route('home');
        }

        Session::flash('error', 'Username atau Password salah!');
        return redirect()->route('login');
    }
  
    public function logout(Request $r)
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
