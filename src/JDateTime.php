<?php

namespace Hamrahnegar\Crawler;

class JDateTime
{

    /**
     * Defaults
     */
    private static $jalali   = true; //Use Jalali Date, If set to false, falls back to gregorian
    private static $convert  = true; //Convert numbers to Farsi characters in utf-8
    private static $timezone = null; //Timezone String e.g Asia/Tehran, Defaults to Server Timezone Settings
    private static $temp = array();

    public function __construct($convert = null, $jalali = null, $timezone = null)
    {
        if ( $jalali   !== null ) self::$jalali   = (bool) $jalali;
        if ( $convert  !== null ) self::$convert  = (bool) $convert;
        if ( $timezone !== null ) self::$timezone = $timezone;
    }

    
    public static function date($format, $stamp = false, $convert = null, $jalali = null, $timezone = null)
    {
        //Timestamp + Timezone
        $stamp    = ($stamp !== false) ? $stamp : time();
        $timezone = ($timezone != null) ? $timezone : ((self::$timezone != null) ? self::$timezone : date_default_timezone_get());
        $obj      = new \DateTime('@' . $stamp, new \DateTimeZone($timezone));
        $obj->setTimezone(new \DateTimeZone($timezone));

        if ( (self::$jalali === false && $jalali === null) || $jalali === false ) {
            return $obj->format($format);
        }
        else {
            
            //Find what to replace
            $chars  = (preg_match_all('/([a-zA-Z]{1})/', $format, $chars)) ? $chars[0] : array();
            
            //Intact Keys
            $intact = array('B','h','H','g','G','i','s','I','U','u','Z','O','P');
            $intact = self::filterArray($chars, $intact);
            $intactValues = array();

            foreach ($intact as $k => $v) {
                $intactValues[$k] = $obj->format($v);
            }
            //End Intact Keys


            //Changed Keys
            list($year, $month, $day) = array($obj->format('Y'), $obj->format('n'), $obj->format('j'));
            list($jyear, $jmonth, $jday) = self::toJalali($year, $month, $day);

            $keys   = array('d','D','j','l','N','S','w','z','W','F','m','M','n','t','L','o','Y','y','a','A','c','r','e','T');
            $keys   = self::filterArray($chars, $keys, array('z'));
            $values = array();

            foreach ($keys as $k => $key) {

                $v = '';
                switch ($key) {
                    //Day
                    case 'd':
                        $v = sprintf('%02d', $jday);
                        break;
                    case 'D':
                        $v = self::getDayNames($obj->format('D'), true);
                        break;
                    case 'j':
                        $v = $jday;
                        break;
                    case 'l':
                        $v = self::getDayNames($obj->format('l'));
                        break;
                    case 'N':
                        $v = self::getDayNames($obj->format('l'), false, 1, true);
                        break;
                    case 'S':
                        $v = 'ام';
                        break;
                    case 'w':
                        $v = self::getDayNames($obj->format('l'), false, 1, true) - 1;
                        break;
                    case 'z':
                        if ($jmonth > 6) {
                            $v = 186 + (($jmonth - 6 - 1) * 30) + $jday;
                        }
                        else {
                            $v = (($jmonth - 1) * 31) + $jday;
                        }
                        self::$temp['z'] = $v;
                        break;
                    //Week
                    case 'W':
                        $v = is_int(self::$temp['z'] / 7) ? (self::$temp['z'] / 7) : intval(self::$temp['z'] / 7 + 1);
                        break;
                    //Month
                    case 'F':
                        $v = self::getMonthNames($jmonth);
                        break;
                    case 'm':
                        $v = sprintf('%02d', $jmonth);
                        break;
                    case 'M':
                        $v = self::getMonthNames($jmonth, true);
                        break;
                    case 'n':
                        $v = $jmonth;
                        break;
                    case 't':
                    	if ($jmonth>=1 && $jmonth<=6) $v=31;
                    	else if ($jmonth>=7 && $jmonth<=11) $v=30;
                    	else if($jmonth==12 && $jyear % 4 ==3) $v=30;
                    	else if ($jmonth==12 && $jyear % 4 !=3) $v=29;
                        break;
                    //Year
                    case 'L':
                        $tmpObj = new \DateTime('@'.(time()-31536000));
                        $v = $tmpObj->format('L');
                        break;
                    case 'o':
                    case 'Y':
                        $v = $jyear;
                        break;
                    case 'y':
                        $v = $jyear % 100;
                        break;
                    //Time
                    case 'a':
                        $v = ($obj->format('a') == 'am') ? 'ق.ظ' : 'ب.ظ';
                        break;
                    case 'A':
                        $v = ($obj->format('A') == 'AM') ? 'قبل از ظهر' : 'بعد از ظهر';
                        break;
                    //Full Dates
                    case 'c':
                        $v  = $jyear.'-'.sprintf('%02d', $jmonth).'-'.sprintf('%02d', $jday).'T';
                        $v .= $obj->format('H').':'.$obj->format('i').':'.$obj->format('s').$obj->format('P');
                        break;
                    case 'r':
                        $v  = self::getDayNames($obj->format('D'), true).', '.sprintf('%02d', $jday).' '.self::getMonthNames($jmonth, true);
                        $v .= ' '.$jyear.' '.$obj->format('H').':'.$obj->format('i').':'.$obj->format('s').' '.$obj->format('P');
                        break;
                    //Timezone
                    case 'e':
                        $v = $obj->format('e');
                        break;
                    case 'T':
                        $v = $obj->format('T');
                        break;

                }
                $values[$k] = $v;

            }
            //End Changed Keys

            //Merge
            $keys   = array_merge($intact, $keys);
            $values = array_merge($intactValues, $values);

            //Return
            $ret = strtr($format, array_combine($keys, $values));
            return
                ($convert === false ||
                ($convert === null && self::$convert === false) ||
                ( $jalali === false || $jalali === null && self::$jalali === false ))
                ? $ret : self::convertNumbers($ret);

        }

    }

    
    public static function gDate($format, $stamp = false, $timezone = null)
    {
        return self::date($format, $stamp, false, false, $timezone);
    }
    
    
    public static function strftime($format, $stamp = false, $convert = null, $jalali = null, $timezone = null)
    {
        $str_format_code = array(
            '%a', '%A', '%d', '%e', '%j', '%u', '%w',
            '%U', '%V', '%W',
            '%b', '%B', '%h', '%m',
            '%C', '%g', '%G', '%y', '%Y',
            '%H', '%I', '%l', '%M', '%p', '%P', '%r', '%R', '%S', '%T', '%X', '%z', '%Z',
            '%c', '%D', '%F', '%s', '%x',
            '%n', '%t', '%%'
        );
        
        $date_format_code = array(
            'D', 'l', 'd', 'j', 'z', 'N', 'w',
            'W', 'W', 'W',
            'M', 'F', 'M', 'm',
            'y', 'y', 'y', 'y', 'Y',
            'H', 'h', 'g', 'i', 'A', 'a', 'h:i:s A', 'H:i', 's', 'H:i:s', 'h:i:s', 'H', 'H',
            'D j M H:i:s', 'd/m/y', 'Y-m-d', 'U', 'd/m/y',
            '\n', '\t', '%'
        );

        //Change Strftime format to Date format
        $format = str_replace($str_format_code, $date_format_code, $format);

        //Convert to date
        return self::date($format, $stamp, $convert, $jalali, $timezone);
    }
	
    public static function mktime($hour, $minute, $second, $month, $day, $year, $jalali = null, $timezone = null)
    {
        //Defaults
        $month = (intval($month) == 0) ? self::date('m') : $month;
        $day   = (intval($day)   == 0) ? self::date('d') : $day;
        $year  = (intval($year)  == 0) ? self::date('Y') : $year;

        //Convert to Gregorian if necessary
        if ( $jalali === true || ($jalali === null && self::$jalali === true) ) {
            list($year, $month, $day) = self::toGregorian($year, $month, $day);
        }

        //Create a new object and set the timezone if available
        $date = $year.'-'.sprintf('%02d', $month).'-'.sprintf('%02d', $day).' '.$hour.':'.$minute.':'.$second;

        if ( self::$timezone != null || $timezone != null ) {
            $obj = new \DateTime($date, new \DateTimeZone(($timezone != null) ? $timezone : self::$timezone));
        }
        else {
            $obj = new \DateTime($date);
        }

        //Return
        return $obj->format('U');
    }
    
   
    public static function checkdate($month, $day, $year, $jalali = null)
    {
        //Defaults
        $month = (intval($month) == 0) ? self::date('n') : intval($month);
        $day   = (intval($day)   == 0) ? self::date('j') : intval($day);
        $year  = (intval($year)  == 0) ? self::date('Y') : intval($year);
        
        //Check if its jalali date
        if ( $jalali === true || ($jalali === null && self::$jalali === true) )
        {
            $epoch = self::mktime(0, 0, 0, $month, $day, $year);
            
            if( self::date('Y-n-j', $epoch,false) == "$year-$month-$day" ) {
                $ret = true;
            }
            else{
                $ret = false; 
            }
        }
        else //Gregorian Date
        { 
            $ret = checkdate($month, $day, $year);
        }
        
        //Return
        return $ret;
    }

    /**
     * System Helpers below
     * ------------------------------------------------------
     */
    
    /**
     * Filters out an array
     */
    private static function filterArray($needle, $heystack, $always = array())
    {
        return array_intersect(array_merge($needle, $always), $heystack);
    }
    
    /**
     * Returns correct names for week days
     */
    private static function getDayNames($day, $shorten = false, $len = 1, $numeric = false)
    {
        $days = array(
            'sat' => array(1, 'شنبه'),
            'sun' => array(2, 'یکشنبه'),
            'mon' => array(3, 'دوشنبه'),
            'tue' => array(4, 'سه شنبه'),
            'wed' => array(5, 'چهارشنبه'),
            'thu' => array(6, 'پنجشنبه'),
            'fri' => array(7, 'جمعه')
        );

        $day = substr(strtolower($day), 0, 3);
        $day = $days[$day];

        return ($numeric) ? $day[0] : (($shorten) ? self::substr($day[1], 0, $len) : $day[1]);
    }

    /**
     * Returns correct names for months
     */
    private static function getMonthNames($month, $shorten = false, $len = 3)
    {
        // Convert
        $months = array(
            'فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور', 'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسفند'
        );
        $ret    = $months[$month - 1];

        // Return
        return ($shorten) ? self::substr($ret, 0, $len) : $ret;
    }

    /**
     * Converts latin numbers to farsi script
     */
    private static function convertNumbers($matches)
    {
        $farsi_array   = array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹');
        $english_array = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');

        return str_replace($english_array, $farsi_array, $matches);
    }

    /**
     * Division
     */
    private static function div($a, $b)
    {
        return (int) ($a / $b);
    }

    /**
     * Substring helper
     */
    private static function substr($str, $start, $len)
    {
        if( function_exists('mb_substr') ){
            return mb_substr($str, $start, $len, 'UTF-8');
        }
        else{
            return substr($str, $start, $len * 2);
        }
    }

    /**
     * Gregorian to Jalali Conversion
     * Copyright (C) 2000  Roozbeh Pournader and Mohammad Toossi
     */
    public static function toJalali($g_y, $g_m, $g_d)
    {

        $g_days_in_month = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
        $j_days_in_month = array(31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29);

        $gy = $g_y-1600;
        $gm = $g_m-1;
        $gd = $g_d-1;

        $g_day_no = 365*$gy+self::div($gy+3, 4)-self::div($gy+99, 100)+self::div($gy+399, 400);

        for ($i=0; $i < $gm; ++$i)
            $g_day_no += $g_days_in_month[$i];
        if ($gm>1 && (($gy%4==0 && $gy%100!=0) || ($gy%400==0)))
            $g_day_no++;
        $g_day_no += $gd;

        $j_day_no = $g_day_no-79;

        $j_np = self::div($j_day_no, 12053);
        $j_day_no = $j_day_no % 12053;

        $jy = 979+33*$j_np+4*self::div($j_day_no, 1461);

        $j_day_no %= 1461;

        if ($j_day_no >= 366) {
            $jy += self::div($j_day_no-1, 365);
            $j_day_no = ($j_day_no-1)%365;
        }

        for ($i = 0; $i < 11 && $j_day_no >= $j_days_in_month[$i]; ++$i)
            $j_day_no -= $j_days_in_month[$i];
        $jm = $i+1;
        $jd = $j_day_no+1;

        return array($jy, $jm, $jd);

    }

    /**
     * Jalali to Gregorian Conversion
     * Copyright (C) 2000  Roozbeh Pournader and Mohammad Toossi
     *
     */
    public static function toGregorian($j_y, $j_m, $j_d)
    {

        $g_days_in_month = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
        $j_days_in_month = array(31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29);

        $jy = $j_y-979;
        $jm = $j_m-1;
        $jd = $j_d-1;

        $j_day_no = 365*$jy + self::div($jy, 33)*8 + self::div($jy%33+3, 4);
        for ($i=0; $i < $jm; ++$i)
            $j_day_no += $j_days_in_month[$i];

        $j_day_no += $jd;

        $g_day_no = $j_day_no+79;

        $gy = 1600 + 400*self::div($g_day_no, 146097);
        $g_day_no = $g_day_no % 146097;

        $leap = true;
        if ($g_day_no >= 36525) {
            $g_day_no--;
            $gy += 100*self::div($g_day_no,  36524);
            $g_day_no = $g_day_no % 36524;

            if ($g_day_no >= 365)
                $g_day_no++;
            else
                $leap = false;
        }

        $gy += 4*self::div($g_day_no, 1461);
        $g_day_no %= 1461;

        if ($g_day_no >= 366) {
            $leap = false;

            $g_day_no--;
            $gy += self::div($g_day_no, 365);
            $g_day_no = $g_day_no % 365;
        }

        for ($i = 0; $g_day_no >= $g_days_in_month[$i] + ($i == 1 && $leap); $i++)
            $g_day_no -= $g_days_in_month[$i] + ($i == 1 && $leap);
        $gm = $i+1;
        $gd = $g_day_no+1;

        return array($gy, $gm, $gd);

    }

}
/*
$date = new jDateTime(true, true);

echo $date->mktime('شنبه 22 آبان 1395 - 21:54:33');*/
