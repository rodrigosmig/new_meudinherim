<?php

namespace App\Services;

use DateTime;

class DashboardService
{
    /**
     * Returns an array of the lasts six months
     *
     * @return array
     */  
    public function getMonths($date): array
    {
        $months = [
            '01' => __('global.months.january'),  
            '02' => __('global.months.february'), 
            '03' => __('global.months.march'), 
            '04' => __('global.months.april'),  
            '05' => __('global.months.may'), 
            '06' => __('global.months.june'), 
            '07' => __('global.months.july'),  
            '08' => __('global.months.august'), 
            '09' => __('global.months.september'), 
            '10' => __('global.months.october'),  
            '11' => __('global.months.november'), 
            '12' => __('global.months.december'), 
        ];

        $new_date           = (new DateTime($date))->modify('-5 months');
        $selected_months    = [];

        for ($i = 0; $i < 6; $i++) {
            $month  = $new_date->format('m');
            $selected_months[] = $months[$month];

            $new_date = $new_date->modify('+1 months');
        }

        return $selected_months;
    }

    /**
     * Return the date stored in the session
     *
     * @return string
     */
    public function getSessionDate(array $request): string
    {
        $string_date    = session('date', now()->format('Y-m-d'));
        $date           = new DateTime($string_date);
        
        if (isset($request['month'])) {
            if ($request['month'] === 'last') {
                $date = $date->modify("-1 month");
            } else {
                $date = $date->modify("+1 month");
            }   
            session(['date' => $date->format('Y-m-d')]);         
        }

        return $date->format('Y-m-d');
    }

    /**
     * Convert date to string for dashboard title
     *
     * @return string
     */
    public function dateToString($date): string
    {
        $date = new DateTime($date);
        $month = __('global.months.' . strtolower($date->format('F')));
        
        return "Dashboard - {$month}, {$date->format('Y')}";
    }
}