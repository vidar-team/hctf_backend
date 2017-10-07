<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class LoggerFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "LoggerService";
    }
}
