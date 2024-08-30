<?php

class Relative_Date
{

    const DAY = 86400;
    public $timestamp; //keep timestamp used to instantiate object
    public $daystamp; //timestamp for that day, 12:00:00
    public $info; //getdate() info (Array)
    public $day_of_week; //string day of week, ex. "Monday"
    public $relative_formatted_date; //string of date compared to today - "Today", "Yesterday", "x days ago", etc.
    public $formatted_date; //string of date compared to today - "Today", "Yesterday", "Wednesday", "Tuesday", etc.


    public function __construct($timestamp = null)
    {
        if (empty($timestamp)) {
            $timestamp = time();
        }
        if (is_string($timestamp)) {
            $timestamp = strtotime($timestamp);
        }

        $this->timestamp = $timestamp;

        $this->info = getdate($timestamp);

        $this->daystamp = mktime(0, 0, 0, $this->info['mon'], $this->info['mday'], $this->info['year']);

        $this->day_of_week = $this->info['weekday']; //"Friday"

        $this->relative_formatted_date = $this->get_formatted_date(true); //"Today"|"Yesterday"|"x days ago"

        $this->formatted_date = $this->get_formatted_date(); //"Today"|"Yesterday"|"Wednesday"
    }

    /**
     *
     * @param int $timestamp [optional]
     * @return the number of days between this App_Date object and a given timestamp (0: today, 1+: future, -1: past)
     */
    public function get_day_difference($timestamp = null)
    {
        if (empty($timestamp)) {
            $timestamp = time();
        }
        //get # of days in first timestamp
        $first = $this->timestamp / self::DAY;

        //get # of days in second timestamp
        $second = $timestamp / self::DAY;

        //get difference in days
        $difference = floor($second) - floor($first);

        return $difference;
    }

    /**
     * gets the previous working day (Friday before a Monday, or M-Th otherwise)
     * @return previous working day as an instance of App_Date
     */
    public function get_previous_working_day()
    {
        $date = $this->info;
        $day_of_month = $date['mday'];
        $day_of_week = $date['wday'];

        if ($day_of_week == 0) {
            //is Sunday, get Friday
            $previous_work_day = $day_of_month - 2;
        } elseif ($day_of_week == 1) {
            //is Monday, get Friday
            $previous_work_day = $day_of_month - 3;
        } else {
            //is any other day of the week, get previous
            $previous_work_day = $day_of_month - 1;
        }

        $timestamp = mktime($date['hours'], $date['minutes'], $date['seconds'], $date['mon'], $previous_work_day, $date['year']);

        $new_date = new App_Date($timestamp);
        return $new_date;
    }

    /**
     * gets the next working day (Monday after a Friday, or other Tues-F otherwise)
     * @return next working day as an instance of App_Date
     */
    public function get_next_working_day()
    {
        $date = $this->info;
        $day_of_month = $date['mday'];
        $day_of_week = $date['wday'];

        if ($day_of_week == 5) {
            //is Friday, get Monday
            $next_work_day = $day_of_month + 3;
        } elseif ($day_of_week == 6) {
            //is Saturday, get Monday
            $next_work_day = $day_of_month + 2;
        } else {
            //is any other day of the week, get previous
            $next_work_day = $day_of_month + 1;
        }

        $timestamp = mktime($date['hours'], $date['minutes'], $date['seconds'], $date['mon'], $next_work_day, $date['year']);

        $new_date = new App_Date($timestamp);
        return $new_date;
    }

    /**
     * A helper function for getting many previous work days (including current day) - NOTE that this returns an array of App_Date objects
     * @param object $number_of_days [optional]
     * @return Array of App_Date instances
     */
    public function get_previous_working_days($number_of_days = 1)
    {
        $working_days = array();
        //beginning date is this
        $current_date = $this;

        for ($i = 0; $i < $number_of_days; $i++) {
            $working_days[] = $current_date;
            $new_date = new App_Date($current_date->timestamp);

            $current_date = $new_date->get_previous_working_day();
        }
        return $working_days;
    }

    /**
     * Get the formatted string based on relationship of current object's week to the current (actual) week
     * @return formatted string
     */
    public function get_formatted_week()
    {
        $difference = $this->get_week_difference();

        if ($difference == 0) {
            return "This Week";
        } elseif ($difference == -1) {
            return "Last Week";
        } elseif ($difference == 1) {
            return "Next Week";
        } else {
            $date = $this->info;
            //get day of month
            $mday = $date['mday'];
            //get day of week
            $wday = $date['wday'];
            //get the day of the month that is the Sunday of the current week
            $sunday = $mday - $wday;
            $timestamp = mktime($date['hours'], $date['minutes'], $date['seconds'], $date['mon'], $sunday, $date['year']);
            return 'Week of ' . date('F j, Y', $timestamp);
        }
    }

    /**
     * get the difference in weeks between two timestamps, within the context of a business week
     * e.g. A Monday of the current week is in a different week than the previous Friday, despite the difference being < 7 days * @param object $timestamp [optional] * @return */ public function get_week_difference($timestamp = null)
    {
        if (empty($timestamp)) {
            $timestamp = time();
        } //date('W') assumes week starts on Monday $week_of_year=date('W', $this->timestamp);
        $other_week_of_year = date('W', $timestamp);

        $difference = $week_of_year - $other_week_of_year;

        return $difference;
    }

    /**
     * Return formatted string determining if $this date is "Tomorrow", "Today", "Yesterday" or "X days ago"
     * @return a string representing how "now" compares to calling object
     */
    public function get_formatted_date($relative = false, $unix_date = null)
    {
        if (empty($unix_date)) {
            $unix_date = $this->timestamp;
        }

        $periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
        $lengths = array("60", "60", "24", "7", "4.35", "12", "10");

        $now = time();

        // is it future date or past date
        if ($now > $unix_date) {
            //past date
            $difference = $now - $unix_date;
            $tense = "ago";
        } else {
            //future date
            $difference = $unix_date - $now;
            $tense = "from now";
        }

        $formatted_date = "Incorrect format.";

        $day_difference = $this->get_day_difference();

        //if difference is less than a day, check for yesterday, tomorrow, or today;
        if ($day_difference <= 1 && $day_difference >= -1) {
            if ($day_difference == 1) {
                $formatted_date = "Yesterday";
            } elseif ($day_difference == 0) {
                $formatted_date = "Today";
            } else {
                $formatted_date = "Tomorrow";
            }
        } elseif ($relative) {
            //difference is more than a day and the format should be "relative"
            for ($j = 0; $difference >= $lengths[$j] && $j < count($lengths) - 1; $j++) {
                $difference /= $lengths[$j];
            }
            $difference = round($difference);
            if ($difference != 1) {
                $periods[$j] .= "s";
            }
            $formatted_date = "$difference $periods[$j] {$tense}";
        } else { //difference is more than a day and the format should NOT be "relative" $formatted_date=$this->day_of_week;
        }

        return $formatted_date;
    }
}
