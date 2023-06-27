<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index(Request $r)
    {
        $role = 'Admin';
        $gen = DB::table('generations')->select('gen')->where('active', 'Y')->get();
        $event = DB::table('events')->select('id','name')->where('active', 'Y')->get();
        return view('home',['role' => $role,'gen' => $gen,'event' => $event]);
    }
}
