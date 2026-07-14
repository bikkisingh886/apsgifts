<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('get_express_dates')) {
    /**
     * Get available delivery dates for Express delivery (Today, Tomorrow, and next 5 working days).
     * Excludes Sundays.
     *
     * @return array
     */
    function get_express_dates() {
        $dates = array();
        $tz = new DateTimeZone('Asia/Kolkata');
        $current_time = new DateTime('now', $tz);
        
        // 6 PM (18:00) is the cutoff for same day delivery.
        $cutoff_hour = 18;
        $current_hour = (int)$current_time->format('H');
        
        $temp_date = clone $current_time;
        if ($current_hour >= $cutoff_hour) {
            $temp_date->modify('+1 day');
        }
        
        $added_count = 0;
        // Loop to find next 7 valid delivery dates (excluding Sundays)
        for ($i = 0; $i < 15; $i++) {
            if ($added_count >= 7) {
                break;
            }
            
            $day_of_week = $temp_date->format('N'); // 1 (Monday) to 7 (Sunday)
            if ($day_of_week != 7) { // Skip Sunday
                $date_val = $temp_date->format('Y-m-d');
                $label = '';
                
                $today_str = (new DateTime('now', $tz))->format('Y-m-d');
                $tomorrow_str = (new DateTime('now', $tz))->modify('+1 day')->format('Y-m-d');
                
                if ($date_val === $today_str) {
                    $label = 'Today (' . $temp_date->format('d M') . ')';
                } elseif ($date_val === $tomorrow_str) {
                    $label = 'Tomorrow (' . $temp_date->format('d M') . ')';
                } else {
                    $label = $temp_date->format('D, d M');
                }
                
                $dates[] = array(
                    'value' => $date_val,
                    'label' => $label
                );
                $added_count++;
            }
            $temp_date->modify('+1 day');
        }
        
        return $dates;
    }
}

if (!function_exists('calculate_courier_eta')) {
    /**
     * Calculate Expected Time of Arrival (ETA) for Courier Delivery.
     * Formula: Order date + 7 working days (skipping Sundays).
     *
     * @param string $order_date (Y-m-d format)
     * @return string
     */
    function calculate_courier_eta($order_date = NULL) {
        $tz = new DateTimeZone('Asia/Kolkata');
        if ($order_date) {
            $date = new DateTime($order_date, $tz);
        } else {
            $date = new DateTime('now', $tz);
        }
        
        $working_days_to_add = 7;
        while ($working_days_to_add > 0) {
            $date->modify('+1 day');
            if ($date->format('N') != 7) { // Skip Sundays
                $working_days_to_add--;
            }
        }
        
        return $date->format('D, d M Y');
    }
}
