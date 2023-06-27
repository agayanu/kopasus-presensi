<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Carbon;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Contracts\Encryption\DecryptException;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PresenceExport;

class PresenceController extends Controller
{
    public function index(Request $r)
    {
        $event = DB::table('events')->select('id','name')->where('active', 'Y')->get();
        $division = DB::table('divisions')->select('code')->get();
        $eventSelect = $r->input('eventselect') ?? null;
        $divSelect = $r->input('divselect') ?? null;
        return view('operator.presence',['event' => $event,'division' => $division,'eventSelect' => $eventSelect,'divSelect' => $divSelect]);
    }

    public function data(Request $r)
    {
        $eventSelect = $r->input('eventselect') ?? null;
        $divSelect = $r->input('divselect') ?? null;
        if($r->ajax())
        {
            $data = DB::table('registers as a')
                ->join('students as b', 'a.student','=','b.id')
                ->join('events as c', 'a.event','=','c.id')
                ->select('a.id','b.name','b.division','b.class','c.name as event','a.user','a.created_at','a.presence_at','a.home_at');
            if($eventSelect) { $data = $data->where('a.event', $eventSelect); }
            if($divSelect) { $data = $data->where('b.division', $divSelect); }
            $dataCount = $data->count();
            $data      = $data->get();

            if(empty($dataCount))
            {
                $data_fix = [];
                return DataTables::of($data_fix)->make(true);
            }

            foreach ( $data as $d ) {
                $ca = date('d-m-Y H:i:s', strtotime($d->created_at));
                if($d->presence_at) {
                    $pa = date('d-m-Y H:i:s', strtotime($d->presence_at));
                } else {
                    $pa = null;
                }
                if($d->home_at) {
                    $ha = date('d-m-Y H:i:s', strtotime($d->home_at));
                } else {
                    $ha = null;
                }
                $idEnc = Crypt::encryptString($d->id);
                $data_fix[] = [
                    'id'       => $idEnc,
                    'name'     => $d->name,
                    'division' => $d->division,
                    'class'    => $d->class,
                    'event'    => $d->event,
                    'user'     => $d->user,
                    'create'   => $ca,
                    'presence' => $pa,
                    'home'     => $ha
                ];
            }

            return DataTables::of($data_fix)
            ->addColumn('action', function($row){
                $showBtn = '<button class="btn btn-sm btn-success tooltips" type="button" data-coreui-toggle="modal" data-coreui-target="#show" 
                    data-coreui-name="'.$row['name'].'" data-coreui-division="'.$row['division'].'" data-coreui-class="'.$row['class'].'"
                    data-coreui-event="'.$row['event'].'" data-coreui-user="'.$row['user'].'" data-coreui-create="'.$row['create'].'" 
                    data-coreui-presence="'.$row['presence'].'" data-coreui-home="'.$row['home'].'">
                    <i class="cil-book" style="font-weight:bold"></i><span class="tooltiptext">Detail</span></button>
                    ';
                $printBtn = '<a href="'.route('presence.print',['id' => $row['id']]).'" target="_blank" class="btn btn-sm btn-secondary tooltips">
                    <i class="cil-print" style="font-weight:bold"></i><span class="tooltiptext">Print</span></a>
                    ';
                $actionBtn = $showBtn.$printBtn;
                return $actionBtn;
            })
            ->rawColumns(['action'])->make(true);
        }
    }

    public function scan()
    {
        return view('operator.scan');
    }

    public function presence($key)
    {
        $event = DB::table('events')->select('id')->where('active', 'Y')->first();
        $check = DB::table('registers')->where([['nrp', $key],['event', $event->id]])->count();
        if(!$check) {
            return redirect()->back()->with('error', 'Gagal! Kode tidak ditemukan!');
        }
        $d = Db::table('students')->select('name')->where('nrp', $key)->first();
        $reg = DB::table('registers')->select('id')->where([['nrp', $key],['event', $event->id]])->first();
        $check = DB::table('registers')->where('id', $reg->id)->whereNull('presence_at')->count();
        if($check) {
            DB::table('registers')->where('id', $reg->id)->update(['presence_at' => now()]);
            return redirect()->back()->with('success', 'Berhasil melakukan presensi masuk '.$d->name);
        }
        $check = DB::table('registers')->where('id', $reg->id)->whereNull('home_at')->count();
        if($check) {
            $now = now();
            $cTime = DB::table('registers')->select('presence_at')->where('id', $reg->id)->first();
            $presenceAt = Carbon::parse($cTime->presence_at);
            $diffMinutes = $presenceAt->diffInMinutes($now);

            if($diffMinutes < 60) {
                return back()->with('error','Gagal! Anda Belum Boleh Pulang!');
            }
            
            DB::table('registers')->where('id', $reg->id)->update(['home_at' => now()]);
            return redirect()->back()->with('success', 'Berhasil melakukan presensi pulang '.$d->name);
        }
        return redirect()->back()->with('error', 'Gagal! Anda sudah melakukan presensi pulang!');
    }

    public function presence_form(Request $r)
    {
        $key = $r->input('codeqr') ?? null;
        if(!$key) {
            return redirect()->back()->with('error', 'Gagal! Kode QR wajib diisi!');
        }
        $event = DB::table('events')->select('id')->where('active', 'Y')->first();
        $check = DB::table('registers')->where([['nrp', $key],['event', $event->id]])->count();
        if(!$check) {
            return redirect()->back()->with('error', 'Gagal! Kode tidak ditemukan!');
        }
        $d = Db::table('students')->select('name')->where('nrp', $key)->first();
        $reg = DB::table('registers')->select('id')->where([['nrp', $key],['event', $event->id]])->first();
        $check = DB::table('registers')->where('id', $reg->id)->whereNull('presence_at')->count();
        if($check) {
            DB::table('registers')->where('id', $reg->id)->update(['presence_at' => now()]);
            return redirect()->back()->with('success', 'Berhasil melakukan presensi masuk '.$d->name);
        }
        $check = DB::table('registers')->where('id', $reg->id)->whereNull('home_at')->count();
        if($check) {
            $now = now();
            $cTime = DB::table('registers')->select('presence_at')->where('id', $reg->id)->first();
            $presenceAt = Carbon::parse($cTime->presence_at);
            $diffMinutes = $presenceAt->diffInMinutes($now);

            if($diffMinutes < 60) {
                return back()->with('error','Gagal! Anda Belum Boleh Pulang!');
            }
            
            DB::table('registers')->where('id', $reg->id)->update(['home_at' => now()]);
            return redirect()->back()->with('success', 'Berhasil melakukan presensi pulang '.$d->name);
        }
        return redirect()->back()->with('error', 'Gagal! Anda sudah melakukan presensi pulang!');   
    }

    public function show()
    {
        $e = DB::table('events')->select('id')->where('active', 'Y')->first();
        return view('operator.show',['event' => $e->id]);
    }

    public function show_data(Request $r)
    {
        if($r->ajax()) {
            $event = $r->input('events') ?? null;
            $check = DB::table('registers')->where('event', $event)->whereNotNull('presence_at')->count();
            if($check) {
                $data = DB::table('registers as a')->join('students as b', 'a.student','=','b.id')
                    ->select('b.name','b.division','b.class','a.presence_at','a.home_at')
                    ->whereNotNull('a.presence_at')->orderBy('a.home_at', 'desc')->orderBy('a.presence_at', 'desc')->limit(10)->get();
    
                foreach ($data as $d) {
                    $pa = date('d-m-Y H:i:s', strtotime($d->presence_at));
                    if($d->home_at) {
                        $ha = date('d-m-Y H:i:s', strtotime($d->home_at));
                    } else {
                        $ha = '';
                    }
                    $dataFix[] = [
                        'name'     => $d->name,
                        'division' => $d->division,
                        'class'    => $d->class,
                        'presence' => $pa,
                        'home'     => $ha,
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

        $s = DB::table('registers as a')->join('students as b', 'a.student','=','b.id')->select('a.nrp','b.name')->where('a.id', $id)->first();
        $customPaper = array(0,0,481.8897638,623.6220472);
        $pdf = Pdf::loadView('pdf', ['id'=>$id,'nrp'=>$s->nrp,'name'=>$s->name])->setPaper($customPaper, 'potrait');
        return $pdf->stream('QrCode Pesat Pelepasan '.$s->name.'.pdf');
    }

    public function download(Request $r)
    {
        $event    = $r->input('event_d') ?? null;
        $division = $r->input('div_d') ?? null;

        $data = DB::table('registers as a')
            ->join('students as b', 'a.student','=','b.id')
            ->join('events as c', 'a.event','=','c.id')
            ->select('a.nrp','b.name','b.division','b.class','c.name as event','a.created_at','a.presence_at','a.home_at');
        if($event) { $data = $data->where('a.event', $event); }
        if($division) { $data = $data->where('b.division', $division); }
        $data = $data->get();

        return Excel::download(new PresenceExport($data), 'Presensi Kopasus.xlsx');
    }
}
