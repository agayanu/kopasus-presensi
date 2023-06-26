<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ChangePassController extends Controller
{
    public function index()
    {
        return view('change-pass');
    }

    public function update(Request $r)
    {
        $rules = [
            'password' => 'required|confirmed|string',
        ];
    
        $messages = [
            'password.required'  => 'Password wajib diisi',
            'password.confirmed' => 'Password harus sama dengan konfirmasi',
            'password.string'    => 'Password harus string',
        ];
  
        $validator = Validator::make($r->all(), $rules, $messages);

        if($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput($r->all);
        }

        $id = Auth::user()->id;
        $pass = $r->input('password');
        DB::table('users')->where('id', $id)->update([
            'password'   => Hash::make($pass),
            'updated_at' => now()
        ]);

        return redirect()->back()->with('success', 'Berhasil ganti password');
    }
}
