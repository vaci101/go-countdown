<?php
class GO_Getdelta{
		
	public $now_year;
	public $now_month;
	public $now_day;
	public $now_hour ;
	public $now_minute;
	public $now_second;
	
	public $target_year;
	public $target_month;
	public $target_day;
	public $target_hour ;
	public $target_minute;
	public $target_second;
	
	public $year;
	public $minute;
	public $day;
	public $hour;
	public $iminute;
	public $seconds;
	
	public $week;
	
	public $delta;
	
	public function __construct($target_time, $now_time){
		$this->now_year = date("Y", $now_time);
		$this->now_month = date("m", $now_time);
		$this->now_day = date("d", $now_time);
		$this->now_hour = date("H", $now_time);
		$this->now_minute = date("i", $now_time);
		$this->now_second = date("s", $now_time);
		
		$this->target_year = date("Y", $target_time);
		$this->target_month = date("m", $target_time);
		$this->target_day = date("d", $target_time);
		$this->target_hour = date("H", $target_time);
		$this->target_minute = date("i", $target_time);
		$this->target_second = date("s", $target_time);
		
		$this->year = $this->target_year - $this->now_year;
		$this->month  = $this->target_month - $this->now_month;
		$this->day = $this->target_day - $this->now_day;
		$this->hour  = $this->target_hour - $this->now_hour;
		$this->iminute = $this->target_minute - $this->now_minute;
		$this->seconds = $this->target_second - $this->now_second;
		
		$this->delta = $target_time - $now_time;
	}
}