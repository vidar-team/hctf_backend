<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

class SystemController extends Controller
{
    public function getMetaInfo(Request $request)
    {
        return \APIReturn::success([
            "startTime" => Carbon::parse(env("HCTF_START_TIME"))->toIso8601String(),
            "endTime" => Carbon::parse(env("HCTF_END_TIME"))->toIso8601String()
        ]);
    }
}
