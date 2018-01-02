<?php
namespace App\Services;
class ScoreService
{
    static function calculate($solvedCount, $ctfPattern = 'hctf', $baseScore = 1000, $minimumScore = 10){
        if ($ctfPattern === 'hctf'){
            if ($solvedCount === 1){
                return $baseScore;
            }
            $score = ($baseScore / 1000) * (-28730 * pow($solvedCount, 0.007236) + 29840);
            if ($score <= $minimumScore){
                return $minimumScore;
            }
            return round($score, 2);

        } elseif ($ctfPattern === 'hgame'){
            switch ($solvedCount){
                case 1:
                    return round($baseScore*1.05, 2);
                case 2:
                    return round($baseScore*1.03, 2);
                case 3:
                    return round($baseScore*1.01, 2);
                default:
                    return $baseScore;
            }
        }
    }
}