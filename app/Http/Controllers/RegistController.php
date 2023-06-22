<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class RegistController extends Controller
{
    public function index()
    {
        return view('regist');
    }

    public function student(Request $r)
    {
        $year = now()->format('Y');
        if($r->ajax())
        {
            $studentx = DB::table('registers')->select('student')->where('year', $year)->get();
            $student = [];
            foreach ($studentx as $s) { 
                array_push($student, $s->student);
            }
            $data = DB::table('students')
                ->select('id','name','class')
                ->where([['name','LIKE','%'.$r->term.'%'],['year', $year]])
                ->whereNotIn('id', $student)
                ->paginate(10, ['*'], 'page', $r->page);

            return response()->json([$data]);
        }
    }

    public function store(Request $r)
    {
        $rules = [
            'student' => 'required|integer',
            'parent'  => 'required|string',
        ];
    
        $messages = [
            'student.required' => 'Nama Siswa wajib diisi',
            'student.integer'  => 'Nama Siswa tidak sesuai pilihan',
            'parent.required'  => 'Nama Lengkap Orangtua wajib diisi',
            'parent.string'    => 'Nama Lengkap Orangtua harus teks',
        ];
  
        $validator = Validator::make($r->all(), $rules, $messages);

        if($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput($r->all);
        }

        $year = now()->format('Y');
        $student = $r->input('student');
        $parent = $r->input('parent');
        $check = DB::table('registers')->where('student', $student)->count();
        if($check) {
            return redirect()->back()->with('error', 'Anda sudah registrasi! Silahkan hubungi Operator!');
        }
        $s = DB::table('students')->select('name','seat_number','seat_number_parent')->where('id', $student)->first();
        // $seatNumber = 0;
        // $checkSn = DB::table('registers')->where('year', $year)->count();
        // if($checkSn) {
        //     $checkMax = DB::table('registers')->where('year', $year)->max('seat_number');
        //     $seatNumber = $checkMax + 1;
        // } else {
        //     $seatNumber = 1;
        // }
        // $seatNumberText = str_pad($seatNumber, 3, "0", STR_PAD_LEFT);
        do {
            $uuidx = Str::random(6);
            $uuid = strtoupper($uuidx);
        } while (DB::table('registers')->where('uuid', $uuid)->exists());

        // $id = DB::table('registers')->insertGetId([
        //     'student'     => $student,
        //     'parent'      => $parent,
        //     'year'        => $year,
        //     'uuid'        => $uuid,
        //     'seat_number' => $seatNumber,
        //     'created_at'  => now(),
        // ]);
        $id = DB::table('registers')->insertGetId([
            'student'     => $student,
            'parent'      => $parent,
            'year'        => $year,
            'uuid'        => $uuid,
            'user'        => $s->name,
            'created_at'  => now(),
        ]);

        $image = QrCode::format('png')->size(500)->generate($uuid);
        Storage::disk('local')->put('public/images/qrcode/'.$id.'.png', $image);
        
        $customPaper = array(0,0,481.8897638,623.6220472);
        $pdf = Pdf::loadView('pdf', ['id'=>$id,'seatNumber'=>$s->seat_number,'seatNumberParent'=>$s->seat_number_parent,'uuid'=>$uuid,'name'=>$s->name])->setPaper($customPaper, 'potrait');
        return $pdf->stream('QrCode Pesat Pelepasan '.$s->name.'.pdf');
    }
}
