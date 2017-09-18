<?php
namespace App\Services\Rules;
interface BaseRule{
    public function check($logs);
}