<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Session;

class SessionController extends Controller
{

    public function storeSession(Request $request)
    {
       
        session()->flush();
     
    }
}
