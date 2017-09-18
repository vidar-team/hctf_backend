<?php

namespace App\Http\Controllers;

use App\Category;
use App\Level;
use App\Services\RuleValidator;
use App\Team;
use Illuminate\Http\Request;
use APIReturn;

class IndexController extends Controller
{
    public function index(Request $request)
    {
        $title = 'hctf';
        return APIReturn::success([
            'hello' => $title
        ]);
    }
}
