<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use APIReturn;

class IndexController extends Controller
{
    public function index(Request $request)
    {
        return APIReturn::success([
            'hello' => 'hctf'
        ]);
    }
}
