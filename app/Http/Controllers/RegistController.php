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
        if($r->ajax())
        {
            $gen = DB::table('generations')->select('gen')->where('active', 'Y');
            $event = DB::table('events')->select('id')->where('active', 'Y')->first();
            $student = DB::table('registers')->select('student')->where('event', $event->id);
            $data = DB::table('students')
                ->select('id','name','class')
                ->where('name','LIKE','%'.$r->term.'%')
                ->whereIn('gen', $gen)
                ->whereNotIn('id', $student)
                ->paginate(10, ['*'], 'page', $r->page);

            return response()->json([$data]);
        }
    }

    public function store(Request $r)
    {
        $rules = [
            'student' => 'required|integer',
        ];
    
        $messages = [
            'student.required' => 'Nama Siswa wajib diisi',
            'student.integer'  => 'Nama Siswa tidak sesuai pilihan',
        ];
  
        $validator = Validator::make($r->all(), $rules, $messages);

        if($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput($r->all);
        }

        $event = DB::table('events')->select('id')->where('active', 'Y')->first();
        $student = $r->input('student');
        $check = DB::table('registers')->where('student', $student)->count();
        if($check) {
            return redirect()->back()->with('error', 'Anda sudah registrasi! Silahkan hubungi Operator!');
        }
        $s = DB::table('students')->select('nrp','name')->where('id', $student)->first();

        $id = DB::table('registers')->insertGetId([
            'nrp'         => $s->nrp,
            'student'     => $student,
            'event'       => $event->id,
            'user'        => $s->name,
            'created_at'  => now(),
        ]);

        $image = QrCode::format('png')->size(500)->generate($s->nrp);
        Storage::disk('local')->put('public/images/qrcode/'.$id.'.png', $image);
        
        $customPaper = array(0,0,481.8897638,623.6220472);
        $pdf = Pdf::loadView('pdf', ['id'=>$id,'nrp'=>$s->nrp,'name'=>$s->name])->setPaper($customPaper, 'potrait');
        return $pdf->stream('QrCode Kopasus Presensi '.$s->name.'.pdf');
    }
}
