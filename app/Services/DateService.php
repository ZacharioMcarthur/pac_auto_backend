<?php

namespace App\Services;


use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class DateService {

    /**
     * @param $date
     * @param $time
     * @return \Carbon\Carbon|false
     */
    public static function addTimeToDate($date, $time): Carbon {
        $date = Carbon::createFromFormat("Y-m-d", $date)->startOfDay();
        $timeArray = preg_split("/:/", $time);
        $date = $date->addHours( (int) $timeArray[0]);
        $date = $date->addMinutes( (int) $timeArray[1]);
        return $date;
    }

}
