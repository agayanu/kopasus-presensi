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
        return view('operator.regist-not',['class' => $class,'year' => $year,'classSelect' => $classSelect]);
    }

    public function data(Request $r)
    {
        $year = $r->input('year');
        $class = $r->input('class');
        if($r->ajax())
        {
            $data = DB::table('students as a')
                ->leftJoin('registers as b', 'a.id','=','b.student')
                ->select('a.name','a.class','a.year')
                ->whereNull('b.student');
            if($year) { $data = $data->where('a.year', $year); }
            if($class) { $data = $data->where('a.class', $class); }
            $dataCount = $data->count();
            $data      = $data->get();

            if(empty($dataCount))
            {
                $data_fix = [];
                return DataTables::of($data_fix)->make(true);
            }

            foreach ( $data as $d ) {
                $data_fix[] = [
                    'name'  => $d->name,
                    'class' => $d->class,
                    'year'  => $d->year,
                ];
            }

            return DataTables::of($data_fix)->make(true);
        }
    }

    public function download(Request $r)
    {
        $year  = $r->input('year_d') ?? null;
        $class = $r->input('class_d') ?? null;

        $data = DB::table('students as a')
                ->leftJoin('registers as b', 'a.id','=','b.student')
                ->select('a.name','a.class','a.year')
                ->whereNull('b.student');
        if($year) { $data = $data->where('a.year', $year); }
        if($class) { $data = $data->where('a.class', $class); }
        $data = $data->orderBy('a.class')->orderBy('a.name')->get();

        return Excel::download(new RegistNotExport($data), 'Belum_Regist_Pelepasan.xlsx');
    }
}
