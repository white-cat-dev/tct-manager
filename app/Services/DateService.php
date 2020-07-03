<?php

namespace App\Services;
use Carbon\Carbon;


class DateService
{
    public static function getMonthes()
    {
        return [
            ['id' => 1, 'name' => 'Январь'],
            ['id' => 2, 'name' => 'Февраль'],
            ['id' => 3, 'name' => 'Март'],
            ['id' => 4, 'name' => 'Апрель'],
            ['id' => 5, 'name' => 'Май'],
            ['id' => 6, 'name' => 'Июнь'],
            ['id' => 7, 'name' => 'Июль'],
            ['id' => 8, 'name' => 'Август'],
            ['id' => 9, 'name' => 'Сентябрь'],
            ['id' => 10, 'name' => 'Октябрь'],
            ['id' => 11, 'name' => 'Ноябрь'],
            ['id' => 12, 'name' => 'Декабрь']
        ];
    }

    public static function getYears($query)
    {
        $today = Carbon::today();

        $years = $query->groupBy('date')->get()->groupBy(function($date) {
            return Carbon::parse($date->created_at)->format('Y');
        })->keys();

        $years[] = $today->year - 1;
        $years[] = $today->year;
        $years[] = $today->year + 1;

        $years = $years->unique()->sort()->values();

        return $years;
    }
}