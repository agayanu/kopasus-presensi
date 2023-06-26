<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PresenceExport;

class PresenceController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if(!in_array(Auth::user()->role, ['0','1'])) {
                return redirect()->route('home');
            }

            return $next($request);
        });
    }

    public function index(Request $r)
    {
        $year = $r->input('year') ?? now()->format('Y');
        $classSelect = $r->input('class') ?? null;
        $class = DB::table('students')->distinct()->get('class');
        return view('operator.presence',['class' => $class,'year' => $year,'classSelect' => $classSelect]);
    }

    public function data(Request $r)
    {
        $year = $r->input('year');
        $class = $r->input('class');
        if($r->ajax())
        {
            $data = DB::table('registers as a')
                ->join('students as b', 'a.student','=','b.id')
                ->select('a.id','b.name','b.class','b.year','a.parent','b.seat_number','b.seat_number_parent','a.user','a.created_at','a.updated_at','a.presence_at');
            if($year) { $data = $data->where('b.year', $year); }
            if($class) { $data = $data->where('b.class', $class); }
            $dataCount = $data->count();
            $data      = $data->get();

            if(empty($dataCount))
            {
                $data_fix = [];
                return DataTables::of($data_fix)->make(true);
            }

            foreach ( $data as $d ) {
                $ca = date('d-m-Y H:i:s', strtotime($d->created_at));
                if($d->updated_at) {
                    $ua = date('d-m-Y H:i:s', strtotime($d->updated_at));
                } else {
                    $ua = null;
                }
                if($d->presence_at) {
                    $pa = date('d-m-Y H:i:s', strtotime($d->presence_at));
                } else {
                    $pa = null;
                }
                $idEnc = Crypt::encryptString($d->id);
                $data_fix[] = [
                    'id'               => $idEnc,
                    'name'             => $d->name,
                    'class'            => $d->class,
                    'year'             => $d->year,
                    'parent'           => $d->parent,
                    'seatNumber'       => $d->seat_number,
                    'seatNumberParent' => $d->seat_number_parent,
                    'user'             => $d->user,
                    'creat'            => $ca,
                    'update'           => $ua,
                    'presence'         => $pa
                ];
            }

            return DataTables::of($data_fix)
            ->addColumn('action', function($row){
                $editBtn = '<button class="btn btn-sm btn-success tooltips" type="button" data-coreui-toggle="modal" data-coreui-target="#edit" 
                    data-coreui-id="'.$row['id'].'" data-coreui-name="'.$row['name'].'" data-coreui-classes="'.$row['class'].'" 
                    data-coreui-year="'.$row['year'].'" data-coreui-parent="'.$row['parent'].'" 
                    data-coreui-seatnumber="'.$row['seatNumber'].'" data-coreui-seatnumberparent="'.$row['seatNumberParent'].'" 
                    data-coreui-user="'.$row['user'].'" data-coreui-creat="'.$row['creat'].'" data-coreui-update="'.$row['update'].'" 
                    data-coreui-presence="'.$row['presence'].'">
                    <i class="cil-pencil" style="font-weight:bold"></i><span class="tooltiptext">Edit</span></button>
                    ';
                $printBtn = '<a href="'.route('presence.print',['id' => $row['id']]).'" target="_blank" class="btn btn-sm btn-secondary tooltips">
                    <i class="cil-print" style="font-weight:bold"></i><span class="tooltiptext">Print</span></a>
                    ';
                $actionBtn = $editBtn.$printBtn;
                return $actionBtn;
            })
            ->rawColumns(['action'])->make(true);
        }
    }

    public function update(Request $r)
    {
        $rules = [
            'id'     => 'required|string',
            'parent' => 'required|string',
        ];
    
        $messages = [
            'id.required'      => 'ID tidak ditemukan',
            'id.string'        => 'ID tidak sesuai',
            'parent.required'  => 'Nama Lengkap Orangtua wajib diisi',
            'parent.string'    => 'Nama Lengkap Orangtua harus teks',
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
        
        $parent = $r->input('parent');
        $d = DB::table('registers as a')->join('students as b', 'a.student','=','b.id')->select('b.name')->where('a.id', $id)->first();

        DB::table('registers')->where('id', $id)->update([
            'parent'     => $parent,
            'user'       => Auth::user()->name,
            'updated_at' => now()
        ]);

        return redirect()->back()->with('success', 'Berhasil update data '.$d->name);
    }

    public function scan()
    {
        return view('operator.scan');
    }

    public function presence($key)
    {
        $check = DB::table('registers')->where('uuid', $key)->count();
        if(!$check) {
            return redirect()->back()->with('error', 'Gagal! Kode tidak ditemukan!');
        }
        $check = DB::table('registers')->where('uuid', $key)->whereNotNull('presence_at')->count();
        if($check) {
            return redirect()->back()->with('error', 'Gagal! Anda sudah melakukan presensi!');
        }

        $d = Db::table('registers as a')->join('students as b', 'a.student','=','b.id')->select('b.name')->where('a.uuid', $key)->first();
        DB::table('registers')->where('uuid', $key)->update(['presence_at' => now()]);
        return redirect()->back()->with('success', 'Berhasil melakukan presensi '.$d->name);
    }

    public function presence_form(Request $r)
    {
        $key = $r->input('codeqr') ?? null;
        if(!$key) {
            return redirect()->back()->with('error', 'Gagal! Kode QR wajib diisi!');
        }
        $check = DB::table('registers')->where('uuid', $key)->count();
        if(!$check) {
            return redirect()->back()->with('error', 'Gagal! Kode tidak ditemukan!');
        }
        $check = DB::table('registers')->where('uuid', $key)->whereNotNull('presence_at')->count();
        if($check) {
            return redirect()->back()->with('error', 'Gagal! Anda sudah melakukan presensi!');
        }

        $d = Db::table('registers as a')->join('students as b', 'a.student','=','b.id')->select('b.name')->where('a.uuid', $key)->first();
        DB::table('registers')->where('uuid', $key)->update(['presence_at' => now()]);
        return redirect()->back()->with('success', 'Berhasil melakukan presensi '.$d->name);
    }

    public function show()
    {
        return view('operator.show');
    }

    public function show_data(Request $r)
    {
        if($r->ajax()) {
            $check = DB::table('registers as a')->join('students as b', 'a.student','=','b.id')
            ->select('b.name','b.class','a.parent','b.seat_number','b.seat_number_parent','a.presence_at')
            ->whereNotNull('presence_at')->count();
            if($check) {
                $data = DB::table('registers as a')->join('students as b', 'a.student','=','b.id')
                    ->select('b.name','b.class','a.parent','b.seat_number','b.seat_number_parent','a.presence_at')
                    ->whereNotNull('presence_at')->orderBy('presence_at', 'desc')->limit(10)->get();
    
                foreach ($data as $d) {
                    $pa = date('d-m-Y H:i:s', strtotime($d->presence_at));
                    $dataFix[] = [
                        'name'             => $d->name,
                        'class'            => $d->class,
                        'parent'           => $d->parent,
                        'seatNumber'       => $d->seat_number,
                        'seatNumberParent' => $d->seat_number_parent,
                        'presence'         => $pa,
                    ];
                }
            } else {
                $dataFix = null;
            }

            return response()->json(['data' => $dataFix]);
        }
    }

    public function print($idx)
    {
        try {
            $id = Crypt::decryptString($idx);
        } catch (DecryptException $e) {
            return response()->json('ID Tidak Dikenali!', 400);
        }

        $s = DB::table('registers as a')->join('students as b', 'a.student','=','b.id')->select('b.name','b.seat_number','b.seat_number_parent','a.uuid')->where('a.id', $id)->first();
        $customPaper = array(0,0,481.8897638,623.6220472);
        $pdf = Pdf::loadView('pdf', ['id'=>$id,'seatNumber'=>$s->seat_number,'seatNumberParent'=>$s->seat_number_parent,'uuid'=>$s->uuid,'name'=>$s->name])->setPaper($customPaper, 'potrait');
        return $pdf->stream('QrCode Pesat Pelepasan '.$s->name.'.pdf');
    }

    public function download(Request $r)
    {
        $year  = $r->input('year') ?? null;
        $class = $r->input('classSelect') ?? null;

        $data = DB::table('registers as a')
            ->join('students as b', 'a.student','=','b.id')
            ->select('b.name','b.class','b.year','a.parent','b.seat_number','b.seat_number_parent','a.user','a.created_at','a.updated_at','a.presence_at');
        if($year) { $data = $data->where('b.year', $year); }
        if($class) { $data = $data->where('b.class', $class); }
        $data = $data->get();

        return Excel::download(new PresenceExport($data), 'Kehadiran_Pelepasan.xlsx');
    }
}
