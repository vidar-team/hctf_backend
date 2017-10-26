<?php

namespace App\Http\Controllers;

use App\Category;
use App\Facades\SystemLogFacade;
use App\Level;
use App\Services\RuleValidator;
use App\SystemLog;
use App\Team;
use Carbon\Carbon;
use Illuminate\Http\Request;
use APIReturn;

class IndexController extends Controller
{
    public function index(Request $request)
    {
        Carbon::now()->toDateTimeString();
        $title = 'hctf';
        return APIReturn::success([
            'hello' => "hctf"
        ]);
    }
}
