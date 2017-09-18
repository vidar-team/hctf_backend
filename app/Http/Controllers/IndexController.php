<?php

namespace App\Http\Controllers;

use App\Level;
use App\Services\RuleValidator;
use Illuminate\Http\Request;
use APIReturn;

class IndexController extends Controller
{
    public function index(Request $request)
    {
        $level = Level::find(1);
        $validator = new RuleValidator(1, $level->rules);
//        dd($validator);

        $arr = [[], []];
        dd($validator->check([]));
        $title = 'hctf';
        return APIReturn::success([
            'hello' => $title
        ]);
    }
}
