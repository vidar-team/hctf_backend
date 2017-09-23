<?php

namespace App\Http\Controllers;

use App\SystemLog;
use Illuminate\Http\Request;

class SystemLogController extends Controller
{
    /**
     * 查询最近的 System Log
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author Eridanus Sora <sora@sound.moe>
     */
    public function list(Request $request){
        if (!$request->input("startId")){
            return \APIReturn::success(SystemLog::orderBy('id', 'desc')->take(50)->get());
        }
        else{
            return \APIReturn::success(SystemLog::where('id', '>', $request->input('startId'))->orderBy('id', 'desc')->get());
        }
    }
}
