<?php

if (!function_exists('get_express_dates')) {
    /**
     * Get available delivery dates for Express delivery (Today, Tomorrow, and next 5 working days).
     * Excludes Sundays.
     */
    function get_express_dates(): array {
        $dates = [];
        $tz = new DateTimeZone('Asia/Kolkata');
        $currentTime = new DateTime('now', $tz);
        
        // 6 PM (18:00) is the cutoff for same day delivery.
        $cutoffHour = 18;
        $currentHour = (int)$currentTime->format('H');
        
        $tempDate = clone $currentTime;
        if ($currentHour >= $cutoffHour) {
            $tempDate->modify('+1 day');
        }
        
        $addedCount = 0;
        // Loop to find next 7 valid delivery dates (excluding Sundays)
        for ($i = 0; $i < 15; $i++) {
            if ($addedCount >= 7) {
                break;
            }
            
            $dayOfWeek = $tempDate->format('N'); // 1 (Monday) to 7 (Sunday)
            if ($dayOfWeek != 7) { // Skip Sunday
                $dateVal = $tempDate->format('Y-m-d');
                $label = '';
                
                $todayStr = (new DateTime('now', $tz))->format('Y-m-d');
                $tomorrowStr = (new DateTime('now', $tz))->modify('+1 day')->format('Y-m-d');
                
                if ($dateVal === $todayStr) {
                    $label = 'Today (' . $tempDate->format('d M') . ')';
                } elseif ($dateVal === $tomorrowStr) {
                    $label = 'Tomorrow (' . $tempDate->format('d M') . ')';
                } else {
                    $label = $tempDate->format('D, d M');
                }
                
                $dates[] = [
                    'value' => $dateVal,
                    'label' => $label
                ];
                $addedCount++;
            }
            $tempDate->modify('+1 day');
        }
        
        return $dates;
    }
}

if (!function_exists('calculate_courier_eta')) {
    /**
     * Calculate Expected Time of Arrival (ETA) for Courier Delivery.
     * Formula: Order date + 7 working days (skipping Sundays).
     */
    function calculate_courier_eta(string $orderDate = null): string {
        $tz = new DateTimeZone('Asia/Kolkata');
        if ($orderDate) {
            $date = new DateTime($orderDate, $tz);
        } else {
            $date = new DateTime('now', $tz);
        }
        
        $workingDaysToAdd = 7;
        while ($workingDaysToAdd > 0) {
            $date->modify('+1 day');
            if ($date->format('N') != 7) { // Skip Sundays
                $workingDaysToAdd--;
            }
        }
        
        return $date->format('D, d M Y');
    }
}
