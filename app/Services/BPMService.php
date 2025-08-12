<?php

namespace App\Services;

class BPMService
{
    public static function checkBPM(array $bpmData, int $threshold = 100): bool
    {
        if (count($bpmData) < 2) return false;

        foreach ($bpmData as $reading) {
            if ($reading['bpm'] < $threshold) {
                return false;
            }
        }

        return true;
    }
}
