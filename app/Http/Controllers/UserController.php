<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Encryption\DecryptException;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function index(Request $r)
    {
        return view('admin.user');
    }

    public function data(Request $r)
    {
        if($r->ajax())
        {
            $data = DB::table('users')
                ->select('id','name','username','created_at','updated_at')
                ->whereNull('deleted_at');
            $dataCount = $data->count();
            $data      = $data->get();

            if(empty($dataCount))
            {
                $data_fix = [];
                return DataTables::of($data_fix)->make(true);
            }

            foreach ( $data as $d ) {
                if($d->updated_at) {
                    $ca = date('d-m-Y H:i:s', strtotime($d->updated_at));
                } else {
                    $ca = date('d-m-Y H:i:s', strtotime($d->created_at));
                }
                $idEnc = Crypt::encryptString($d->id);
                $data_fix[] = [
                    'id'       => $idEnc,
                    'name'     => $d->name,
                    'username' => $d->username,
                    'update'   => $ca,
                ];
            }

            return DataTables::of($data_fix)
            ->addColumn('action', function($row){
                $editBtn = '<button class="btn btn-sm btn-success tooltips" type="button" data-coreui-toggle="modal" data-coreui-target="#edit" 
                    data-coreui-id="'.$row['id'].'" data-coreui-name="'.$row['name'].'" data-coreui-username="'.$row['username'].'">
                    <i class="cil-pencil" style="font-weight:bold"></i><span class="tooltiptext">Edit</span></button>
                    ';
                $resetBtn = '<button class="btn btn-sm btn-secondary tooltips" type="button" data-coreui-toggle="modal" data-coreui-target="#reset" 
                    data-coreui-id="'.$row['id'].'" data-coreui-name="'.$row['name'].'" data-coreui-url="'.route('user.reset',['id' => $row['id']]).'">
                    <i class="cil-lock-unlocked" style="font-weight:bold"></i><span class="tooltiptext">Reset Password</span></button>
                    ';
                $delBtn = '<button class="btn btn-sm btn-danger tooltips" type="button" data-coreui-toggle="modal" data-coreui-target="#del" 
                    data-coreui-id="'.$row['id'].'" data-coreui-name="'.$row['name'].'" data-coreui-url="'.route('user.delete',['id' => $row['id']]).'">
                    <i class="cil-trash" style="font-weight:bold"></i><span class="tooltiptext">Hapus</span></button>
                    ';
                $actionBtn = $editBtn.$resetBtn.$delBtn;
                return $actionBtn;
            })
            ->rawColumns(['action'])->make(true);
        }
    }

    public function store(Request $r)
    {
        $rules = [
            'name'     => 'required|string',
            'username' => 'required|string',
        ];
    
        $messages = [
            'name.required'     => 'Nama Lengkap wajib diisi',
            'name.string'       => 'Nama Lengkap harus teks',
            'username.required' => 'Username wajib diisi',
            'username.string'   => 'Username harus teks',
        ];

        $validator = Validator::make($r->all(), $rules, $messages);

        if($validator->fails()){
            $errorMsg = $validator->errors();
            return redirect()->back()->with('errorx', $errorMsg);
        }
        
        $name = $r->input('name');
        $username = $r->input('username');
        $check = DB::table('users')->where('username', $username)->count();
        if($check) {
            return redirect()->back()->with('error', 'Gagal! Username sudah dipakai!');
        }

        DB::table('users')->insert([
            'name'       => $name,
            'username'   => $username,
            'password'   => Hash::make('123456'),
            'created_at' => now()
        ]);

        return redirect()->back()->with('success', 'Berhasil membuat akun '.$name);
    }

    public function update(Request $r)
    {
        $rules = [
            'id'       => 'required|string',
            'name'     => 'required|string',
            'username' => 'required|string',
        ];
    
        $messages = [
            'id.required'       => 'ID tidak ditemukan',
            'id.string'         => 'ID tidak sesuai',
            'name.required'     => 'Nama Lengkap wajib diisi',
            'name.string'       => 'Nama Lengkap harus teks',
            'username.required' => 'Username wajib diisi',
            'username.string'   => 'Username harus teks',
        ];

        $validator = Validator::make($r->all(), $rules, $messages);

        if($validator->fails()){
            $errorMsg = $validator->errors();
            return redirect()->back()->with('errorx', $errorMsg);
        }

        $idx = $r->input('id');
        try {
            $id = Crypt::decryptString($idx);
        } catch (DecryptException $e) {
            return response()->json('ID Tidak Dikenali!', 400);
        }

        $name = $r->input('name');
        $username = $r->input('username');
        $check = DB::table('users')->where([['id', $id],['username', $username]])->count();
        if($check) {
            DB::table('users')->where('id', $id)->update([
                'name'       => $name,
                'updated_at' => now(),
            ]);
        } else {
            $check = DB::table('users')->where('username', $username)->count();
            if($check) {
                return redirect()->back()->with('error', 'Gagal! Username sudah dipakai!');
            }
        }


        DB::table('users')->where('id', $id)->update([
            'name'       => $name,
            'username'   => $username,
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Berhasil ubah akun '.$name);
    }

    public function reset($idx)
    {
        try {
            $id = Crypt::decryptString($idx);
        } catch (DecryptException $e) {
            return redirect()->back()->with('error', 'Gagal! Key tidak sesuai!');
        }

        $d = DB::table('users')->select('name')->where('id', $id)->first();
        DB::table('users')->where('id', $id)->update([
            'password'   => Hash::make('123456'),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Berhasil reset password '.$d->name.' menjadi 123456');
    }

    public function destroy($idx)
    {
        try {
            $id = Crypt::decryptString($idx);
        } catch (DecryptException $e) {
            return redirect()->back()->with('error', 'Gagal! Key tidak sesuai!');
        }

        $d = DB::table('users')->select('name')->where('id', $id)->first();
        DB::table('users')->where('id', $id)->update(['deleted_at' => now()]);

        return redirect()->back()->with('success', 'Berhasil hapus akun '.$d->name);
    }
}
