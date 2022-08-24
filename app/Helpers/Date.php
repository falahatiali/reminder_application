<?php

namespace App\Helpers;

use DatePeriod;
use DateTime;
use DateInterval;
use Exception;

class Date
{
    public static function allMappings()
    {
        return [
            'yearly' => '0 0 1 1 *',
            'annually' => '0 0 1 1 *',
            'monthly' => '0 0 1 * *',
            'weekly' => '0 0 * * 0',
            'daily' => '0 0 * * *',
            'midnight' => '0 0 * * *',
            'hourly' => '0 * * * *',
            'everyMinute' => '* * * * *',
            'everyTwoMinutes' => '*/2 * * * *',
            'everyThreeMinutes' => '*/3 * * * *',
            'everyFourMinutes' => '*/4 * * * *',
            'everyFiveMinutes' => '*/5 * * * *',
            'everyFifteenMinutes' => '*/15 * * * *',
            'everyThirtyMinutes' => '*/30 * * * *',
            'everyTwoHours' => '0 */2 * * *',
            'everyThreeHours' => '0 */3 * * *',
            'everyFourHours' => '0 */4 * * *',
            'everySixHours' => '0 */6 * * *',
        ];
    }

    /**
     * @throws Exception
     */
    public static function range($start, $end, $interval = 'PT30M'): DatePeriod
    {
        return new DatePeriod(
            new DateTime($start), new DateInterval($interval), new DateTime($end)
        );
    }

    public static function frequencies(): array
    {
        return [
            'everyMinute' => 'Every Minute',
            'everyTwoMinutes' => 'Every 2 Minutes',
            'everyThreeMinutes' => 'Every 3 Minutes',
            'everyFourMinutes' => 'Every 4 Minutes',
            'everyFiveMinutes' => 'Every 5 Minutes',
            'everyFifteenMinutes' => 'Every 15 Minutes',
            'everyThirtyMinutes' => 'Every 30 Minutes',
            'everyTwoHours' => 'Every 2 Hours',
            'everyThreeHours' => 'Every 3 Hours',
            'everyFourHours' => 'Every 4 Hours',
            'everySixHours' => 'Every 6 hours',
            'hourly' => 'Every day',
            'daily' => 'Every day',
            'weekly' => 'Every week',
            'monthly' => 'Every month',
            'yearly' => 'Every year',
        ];
    }

    public static function days(): array
    {
        return [
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
            7 => 'Sunday',
        ];
    }

    public static function ordinal($value): string
    {
        $ends = ['th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th'];
        if ((($value % 100) >= 11) && (($value % 100) <= 13)) {
            return $value . 'th';
        }

        return $value . $ends[$value % 10];
    }
}
