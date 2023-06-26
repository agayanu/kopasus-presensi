<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index(Request $r)
    {
        $role = 'Admin';
        $year = $r->input('year') ?? now()->format('Y');
        return view('home',['role' => $role,'year' => $year]);
    }
}
