<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Symfony\Component\HttpFoundation\Session\Session;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    function __construct(Request $request = null) {}
}
