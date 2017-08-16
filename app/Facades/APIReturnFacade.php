<?php
namespace App\Facades;
use Illuminate\Support\Facades\Facade;

class APIReturnFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "APIReturnService";
    }
}
