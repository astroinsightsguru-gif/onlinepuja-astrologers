<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DemoController extends Controller
{
    public function getDemo()
    {
        $data = DB::table('demo')->get();
        return view('demo', compact('data'));
    }
}
