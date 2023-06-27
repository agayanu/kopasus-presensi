<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PresenceSetController extends Controller
{
    public function clear(Request $r)
    {
        $rules = [
            'gen'   => 'required|integer',
            'set'   => 'required|in:regist,presence',
            'event' => 'required|integer',
        ];
    
        $messages = [
            'gen.required'   => 'Angkatan wajib diisi',
            'gen.integer'    => 'Angkatan harus angka',
            'set.required'   => 'Set wajib diisi',
            'set.string'     => 'Set tidak sesuai pilihan',
            'event.required' => 'Acara wajib diisi',
            'event.integer'  => 'Acara tidak sesuai pilihan',
        ];

        $validator = Validator::make($r->all(), $rules, $messages);

        if($validator->fails()){
            return redirect()->back()->with('error', 'Gagal! Terdapat masalah!');
        }

        $gen = $r->input('gen');
        $set = $r->input('set');
        $event = $r->input('event');
        $check = DB::table('registers as a')
            ->join('students as b', 'a.student','=','b.id')
            ->where([['a.event', $event],['b.gen', $gen]])->count();
        if($check) {
            if($set === 'regist')
            {
                DB::table('registers as a')
                    ->join('students as b', 'a.student','=','b.id')
                    ->where([['a.event', $event],['b.gen', $gen]])->delete();
                $info = 'registrasi';
            }
            if($set === 'presence')
            {
                DB::table('registers as a')
                    ->join('students as b', 'a.student','=','b.id')
                    ->where([['a.event', $event],['b.gen', $gen]])->update(['a.presence_at' => null]);
                $info = 'presensi';
            }
            return redirect()->back()->with('success', 'Berhasil menghapus data '.$info.' acara '.$event.' angkatan '.$gen);
        }

        return redirect()->back()->with('error', 'Data tidak ditemukan!');
    }
}
