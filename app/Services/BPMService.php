<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class BPMService
{
    public static function checkBPM(array $bpmData, int $threshold = 100): bool
    {
        if (count($bpmData) < 2) return false;

        $averageBpm =  array_sum($bpmData) / count($bpmData);

        Log::info('Average BPM', ['averageBpm' => $averageBpm]);

        if($averageBpm > $threshold) {
            return false;
        }

        return true;
    }
}
