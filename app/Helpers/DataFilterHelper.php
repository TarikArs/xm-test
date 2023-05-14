<?php 

namespace App\Helpers;

use Carbon\Carbon;

class DataFilterHelper
{
    public static function filterData($prices, $startDate, $endDate)
    {
        return array_filter($prices, function ($value) use ($startDate, $endDate) {
            $date = Carbon::parse($value['date'])->format('Y-m-d');
            return $date >= $startDate && $date <= $endDate && !empty($value['open']) && !empty($value['close']);
        });
    }
}
