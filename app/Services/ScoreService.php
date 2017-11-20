<?php
namespace App\Services;
class ScoreService
{
    static function calculate($solvedCount, $baseScore = 1000, $minimumScore = 10){
        if ($solvedCount === 1){
            return $baseScore;
        }
        $score = ($baseScore / 1000) * (-28730 * pow($solvedCount, 0.007236) + 29840);
        if ($score <= $minimumScore){
            return $minimumScore;
        }
        return round($score, 2);
    }
}