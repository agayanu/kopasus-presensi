<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PresenceSetController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if(Auth::user()->role != '1') {
                return redirect()->route('home');
            }

            return $next($request);
        });
    }

    public function clear(Request $r)
    {
        $rules = [
            'year' => 'required|integer',
            'set'  => 'required|in:regist,presence',
        ];
    
        $messages = [
            'year.required' => 'Tahun wajib diisi',
            'year.string'   => 'Tahun harus angka',
            'set.required'  => 'Set wajib diisi',
            'set.string'    => 'Set tidak sesuai pilihan',
        ];

        $validator = Validator::make($r->all(), $rules, $messages);

        if($validator->fails()){
            return redirect()->back()->with('error', 'Gagal! Terdapat masalah!');
        }

        $year = $r->input('year');
        $set = $r->input('set');
        $check = DB::table('registers')->where('year', $year)->count();
        if($check) {
            if($set === 'regist')
            {
                DB::table('registers')->where('year', $year)->delete();
                $info = 'registrasi';
            }
            if($set === 'presence')
            {
                DB::table('registers')->where('year', $year)->update(['presence_at' => null]);
                $info = 'presensi';
            }
            return redirect()->back()->with('success', 'Berhasil menghapus data '.$info.' tahun '.$year);
        }

        return redirect()->back()->with('error', 'Tidak ada data tahun '.$year);
    }
}
