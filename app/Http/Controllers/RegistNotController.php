<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RegistNotExport;

class RegistNotController extends Controller
{
    public function index(Request $r)
    {
        $event = DB::table('events')->select('id','name')->get();
        $division = DB::table('divisions')->select('code')->get();
        $e = DB::table('events')->select('id')->where('active', 'Y')->first();
        $eventSelect = $r->input('eventselect') ?? $e->id;
        $divSelect = $r->input('divselect') ?? null;
        return view('operator.regist-not',['event' => $event,'division' => $division,'eventSelect' => $eventSelect,'divSelect' => $divSelect]);
    }

    public function data(Request $r)
    {
        $eventSelect = $r->input('eventselect') ?? null;
        $divSelect = $r->input('divselect') ?? null;
        if($r->ajax())
        {
            $dnot = DB::table('registers')
                ->select('student')
                ->where('event', $eventSelect);
            $data = DB::table('students')
                ->select('name','class','division')
                ->whereNotIn('id', $dnot);
            if($divSelect) { $data = $data->where('division', $divSelect); }
            $dataCount = $data->count();
            $data = $data->get();

            if(empty($dataCount))
            {
                $data_fix = [];
                return DataTables::of($data_fix)->make(true);
            }

            foreach ( $data as $d ) {
                $data_fix[] = [
                    'name'     => $d->name,
                    'class'    => $d->class,
                    'division' => $d->division,
                ];
            }

            return DataTables::of($data_fix)->make(true);
        }
    }

    public function download(Request $r)
    {
        $eventSelect = $r->input('event_d') ?? null;
        $divSelect   = $r->input('div_d') ?? null;

        $dnot = DB::table('registers')
            ->select('student')
            ->where('event', $eventSelect);
        $data = DB::table('students')
            ->select('name','class','division')
            ->whereNotIn('id', $dnot);
        if($divSelect) { $data = $data->where('division', $divSelect); }
        $data = $data->orderBy('division')->orderBy('name')->get();

        return Excel::download(new RegistNotExport($data), 'Belum_Regist.xlsx');
    }
}
