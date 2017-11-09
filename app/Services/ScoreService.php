<?php
namespace App\Services;
class ScoreService
{
    static function calculate($solvedCount){
        if ($solvedCount === 1){
            return 1000;
        }
        $score = -28730*pow($solvedCount, 0.007236) + 29840;
        if ($score <= 10){
            return 10;
        }
        return round($score, 2);
    }
}