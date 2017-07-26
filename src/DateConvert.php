<?php

namespace Hamrahnegar\Crawler;

use Hamrahnegar\Crawler\JDateTime;

class DateConvert 
{

	private $dateTime;
	private $year = 1396;
	private $month = 1;
	private $day = 1;
	private $houre = 0;
	private $minute = 0;
	private $second = 0;
	private $format = true;
	private $timeZone = 'Asia/Tehran';


	public function __construct($dateTime)
	{
		$this->dateTime = $dateTime;	
		$this->getDate();

	}

	public function getTimestamp()
	{
		//dd($this->houre, $this->minute, $this->second, $this->month, $this->day, $this->year);
		return JDateTime::mktime($this->houre, $this->minute, $this->second, $this->month, $this->day, $this->year, $this->format, $this->timeZone);
	}

	public function getDateFormat($format = 'c', $time = 0)
	{
		if ($time == 0) {
			$time = $this->getTimestamp();
		}

		return JDateTime::date($format, $time, false, false);
	}

	private function detectFormatDate()
	{
		$this->convertDate();
		$pattrens = [
			'two' => '/[^,]*,[^,]*,[^,]*/is',
			'one' => '/[^,]*,[^,]*/is',
		];

		foreach ($pattrens as $key => $value) {
			if (preg_match($value, $this->dateTime)) {
				return $key;
			}
		}

		if (str_contains($this->dateTime, 'دقیقه')) {
			return 'three';
		}

		if (str_contains($this->dateTime, 'ساعت')) {
			return 'four';
		}

		return false;

	}

	public function getDate()
	{
		$this->convertDate();
		$key = $this->detectFormatDate();
		$months = [
					'1' => 'فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور', 'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسفند'					           			
        		  ];

		switch ($key) {
			case 'two':
				foreach ($months as $key => $value) {

					if (strpos($this->dateTime, $value)) {
						$this->month = $key;
						$ex = explode($value, $this->dateTime);
						if (preg_match_all('/\d{2}/is', $ex[1], $matches)) {
							$this->year = strlen($matches[0][0]) == 2 ? '13' . $matches[0][0] : $matches[0][0];
						}

						if (preg_match_all('/\d{1,2}/is', $ex[0], $matches)) {
							$this->day = $matches[0][0];
						}

						if (preg_match_all('/\d{2}\:\d{2}/is', $this->dateTime, $matches)) {
							list($this->houre, $this->minute) = explode(":", $matches[0][0]);
						}

					}
				}

				break;

			case 'three':
				$time = time();
				
				if (preg_match_all('/\d{1,2}/is', $this->dateTime, $matches)) {
					$minute = $matches[0][0];
					$time = time() - ($minute * 60);
				}

				$this->year = JDateTime::date('Y', $time, false, true);
				$this->month = JDateTime::date('n', $time, false, true);
				$this->day = JDateTime::date('j', $time, false, true);
				$this->houre = JDateTime::date('H', $time, false, true);
				$this->minute = JDateTime::date('i', $time, false, true);
				break;

			case 'four':
				$time = time();

				if (preg_match_all('/\d{1,2}/is', $this->dateTime, $matches)) {
					$houre = $matches[0][0];
					$time = time() - ($houre * 60 * 60);
				}

				$this->year = JDateTime::date('Y', $time, false, true);
				$this->month = JDateTime::date('n', $time, false, true);
				$this->day = JDateTime::date('j', $time, false, true);
				$this->houre = JDateTime::date('H', $time, false, true);
				$this->minute = JDateTime::date('i', $time, false, true);
				break;
		}
	}

    public function convertDate() 
    {		
        $numbers = [ 
						'fa' => ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹', '،'],
						'en' => ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', ','],						
					];
							
        return $this->dateTime = str_replace($numbers['fa'], $numbers['en'], $this->dateTime);			
			
	}

}