<?php

class GO_Countdown_Timer
{
	public $years;
	public $months;
	public $days;
	public $hours;
	public $minutes;
	public $seconds;
	
		//Per instance
	private $event_list;
	private $eventsPresent;
	private $jsUID = array();
	
	//Settings
	private $delete_one_time_events;
	private $time_format;
	private $show_year;
	private $show_month;
	private $show_week;
	private $show_day;
	private $show_hour;
	private $show_minute;
	private $show_second;
	private $strip_zero;
	private $enableJS;
	private $time_since_time;
	private $titleSuffix;
	private $enabledShortcodeExcerpt;	
	
	/**
	 * Initialize the variables, plugin and register hooks.
	 */
	public function __construct()
	{
		$this->show_year = false;
		$this->show_month = false;
		$this->show_week = false;
		$this->show_day = true;
		$this->show_hour = false;
		$this->show_minute = false;
		$this->show_second = false;
		
		add_action( 'init', array( $this, 'initialize_script' ) );
		add_shortcode( 'go_countdown', array( $this, 'shortcode' ) );
		
	} // end __construct

	function initialize_script()
	{
		//enqueue countdown timer js
		wp_register_script('go-countdown-timer', plugins_url(dirname(plugin_basename(__FILE__)) . "/js/". 'go-countdown-timer.js'), array('jquery'), 4 );
		wp_enqueue_script( 'go-countdown-timer');
		wp_register_script('webkit-sprintf', plugins_url(dirname(plugin_basename(__FILE__)) . "/js/" . 'webtoolkit.sprintf.js'),  array('jquery'), 3);
		wp_enqueue_script( 'webkit-sprintf');
	}

	/**
	 * WordPress shortcode handler.  Populate class variables with
	 * attributes
	 * @param array $atts
	 * @return string
	 */
	public function shortcode( $atts )
	{
		global $go_config_countdown;
		
		extract( shortcode_atts( array(
					'date' => '2013-03-15 08:00',
					'format' => 'Y m d h:m:s',
				), $atts ) );
		
		$target_date = new DateTime( $date, new DateTimeZone( $go_config_countdown[ 'go-datetimezone' ] ) );
		$current_date_time = new DateTime( 'now', new DateTimeZone( $go_config_countdown[ 'go-datetimezone' ] ) );

		$diff = abs( $target_date->getTimestamp() - $current_date_time->getTimestamp() );

		//write out time to hidden field
		echo "<input id='go_countdown_target_date' type='hidden' value='" . $target_date->getTimestamp() . "' />";
		echo "<input id='go_countdown_current_date' type='hidden' value='" . $current_date_time->getTimestamp() . "' />";
		
		//output timer
;		return "<div class='go_countdown_timer_event'>" . $this->process_date( $target_date->getTimestamp(), $current_date_time->getTimestamp() ) . "</div>";
		
	} // end shortcode
	
	/**
	 * Returns the numerical part of a single countdown element
	 *
	 * @param $target_time
	 * @param $now_time
	 * @param $realTargetTime
	 * @since 2.1
	 * @access private
	 * @author Andrew Ferguson
	 * @return string The content of the post with the appropriate dates inserted (if any)
	*/
	function process_date ( $target_time, $now_time ) 
	{
			
		$time_delta = new get_delta_time( $target_time, $now_time );

		$rollover = 0;
		$output = '';
		$sig_num_hit = false;
		
		if( $time_delta->seconds < 0 )
		{
			$time_delta->iminute--;
			$time_delta->seconds = 60 + $time_delta->seconds;
		}//if($time_delta->seconds < 0)

		if( $time_delta->iminute < 0 )
		{
			$time_delta->hour--;
			$time_delta->iminute = 60 + $time_delta->iminute;
		}// if( $time_delta->iminute < 0 )

		if( $time_delta->hour < 0 )
		{
			$time_delta->day--;
			$time_delta->hour = 24 + $time_delta->hour;
		}// if( $time_delta->hour < 0 )

		if( $time_delta->day < 0 )
		{
			$time_delta->month--;
			$time_delta->day = $time_delta->day + cal_days_in_month( CAL_GREGORIAN, $time_delta->now_month, $time_delta->now_year ); //Holy crap! When did they introduce this function and why haven't I heard about it??
		}//if( $time_delta->day < 0 )

		if($time_delta->month < 0)
		{
			$time_delta->year--;
			$time_delta->month = $time_delta->month + 12;
		}//if($time_delta->month < 0)

		//Year
		if($this->show_year)
		{
			if( $sig_num_hit || !$this->strip_zero || $time_delta->year )
			{
				$output = '<span class="go_countdown_timer_year go_countdown_timer_timeunit">' . sprintf( _n( "%d year,", "%d years,", $time_delta->year, "go_countdown_timer" ), $time_delta->year ) . "</span> ";
				$sig_num_hit = true;
			}//if( $sig_num_hit || !$this->strip_zero || $time_delta->year )
		}
		else
		{
			$rollover = $time_delta->year*31536000;
		}

		//Month
		if( $this->show_month )
		{
			if( $sig_num_hit || !$this->strip_zero || intval( $time_delta->month + ( $rollover / 2628000 ) ) )
			{
				$time_delta->month = intval( $time_delta->month + ( $rollover / 2628000 ) );
				$output .= '<span class="go_countdown_timer_month go_countdown_timer_timeunit">' . sprintf(  _n( "%d month,", "%d months,", $time_delta->month, "go_countdown_timer" ), $time_delta->month )."</span> ";
				$rollover = $rollover - intval( $rollover / 2628000 ) * 2628000; //(12/31536000)
				$sig_num_hit = true;
			}
		}
		else
		{
			//If we don't want to show months, let's just calculate the exact number of seconds left since all other units of time are fixed (i.e. months are not a fixed unit of time)	
			//If we showed years, but not months, we need to account for those.
			if( $this->show_year )
			{
				$time_delta->delta = $time_delta->delta - $time_delta->year * 31536000;
			}
			
			//Re calculate the resultant times
			$time_delta->week = intval( $time_delta->delta / ( 86400*7 ) ); 
			$time_delta->day = intval( $time_delta->delta / 86400 );
			$time_delta->hour = intval( ( $time_delta->delta - $time_delta->day * 86400 ) / 3600 );
			$time_delta->iminute = intval( ( $time_delta->delta - $time_delta->day * 86400 - $time_delta->hour * 3600 ) / 60 );
			$time_delta->seconds = intval( ( $time_delta->delta - $time_delta->day * 86400 - $time_delta->hour * 3600 - $time_delta->iminute * 60 ) );
			
			//and clear any rollover time
			$rollover = 0;
		}

		//Week (weeks are counted differently becuase we can just take 7 days and call it a week...so we do that)
		if($this->show_week)
		{
			if( $sig_num_hit || !$this->strip_zero || ( ( $time_delta->day + intval( $rollover / 86400) ) / 7 ) )
			{
				$time_delta->week = $time_delta->week + intval( $rollover / 86400 ) / 7;
				$output .= '<span class="go_countdown_timer_week go_countdown_timer_timeunit">' . sprintf( _n( "%d week,", "%d weeks,", ( intval( ( $time_delta->day + intval($rollover/86400) )/7)), "go_countdown_timer" ), ( intval ( ( $time_delta->day + intval( $rollover / 86400 ) ) / 7 )))."</span> ";		
				$rollover = $rollover - intval( $rollover / 86400 ) * 86400;
				$time_delta->day = $time_delta->day - intval( ( $time_delta->day + intval( $rollover / 86400) ) / 7 ) * 7;
				$sig_num_hit = true;
			}
		}

		//Day
		if( $this->show_day )
		{
			if( $sig_num_hit || !$this->strip_zero || ( $time_delta->day + intval( $rollover / 86400 ) ) )
			{
				$time_delta->day = $time_delta->day + intval( $rollover / 86400 );
				$output .= '<span class="go_countdown_timer_day go_countdown_timer_timeunit">' . sprintf( _n( "%d day,", "%d days,",  $time_delta->day, "go_countdown_timer" ), $time_delta->day ) . "</span> ";
				$rollover = $rollover - intval( $rollover / 86400 ) * 86400;
				$sig_num_hit = true;
			}
		}
		else
		{
			$rollover = $rollover + $time_delta->day * 86400;
		}

		//Hour
		if($this->show_hour)
		{
			if($sig_num_hit || !$this->strip_zero || ( $time_delta->hour + intval( $rollover / 3600 ) ) )
			{
				$time_delta->hour = $time_delta->hour + intval( $rollover / 3600 );
				$output .= '<span class="go_countdown_timer_hour go_countdown_timer_timeunit">' . sprintf( _n( "%d hour,", "%d hours,", $time_delta->hour, "go_countdown_timer" ), $time_delta->hour ) . "</span> ";
				$rollover = $rollover - intval( $rollover / 3600 ) * 3600;
				$sig_num_hit = true;
			}
		}
		else
		{
			$rollover = $rollover + $time_delta->hour * 3600;
		}

		//Minute
		if($this->show_minute)
		{
			if($sig_num_hit || !$this->strip_zero || ($time_delta->iminute + intval($rollover/60)) ){
				$time_delta->iminute = $time_delta->iminute + intval($rollover/60);
				$output .= '<span class="go_countdown_timer_minute go_countdown_timer_timeunit">' . sprintf(  _n( "%d minute,", "%d minutes,", $time_delta->iminute, "go_countdown_timer" ), $time_delta->iminute ) . "</span> ";
				$rollover = $rollover - intval($rollover/60)*60;
				$sig_num_hit = true;
			}
		}
		else 
		{
			$rollover = $rollover + $time_delta->iminute*60;
		}

		//Second
		if($this->show_second)
		{
			$time_delta->seconds = $time_delta->seconds + $rollover;
			$output .= '<span class="go_countdown_timer_second go_countdown_timer_timeunit">' . sprintf( _n( "%d second,", "%d seconds,", $time_delta->seconds, "go_countdown_timer" ), $time_delta->seconds) . "</span> ";
		}
		
		//Catch blank statements
		if( $output == "" )
		{
			if($this->show_second)
			{
				$output = '<span class="go_countdown_timer_second go_countdown_timer_timeunit">' . sprintf( _n( "%d second,", "%d seconds,", "0", "go_countdown_timer" ), "0" ) . "</span> ";
			}
			elseif($this->show_minute)
			{
				$output = '<span class="go_countdown_timer_minute go_countdown_timer_timeunit">' . sprintf( _n( "%d second,", "%d seconds,", "0", "go_countdown_timer" ), "0" ) . "</span> ";
			}
			elseif($this->show_hour)
			{
				$output = '<span class="go_countdown_timer_hour go_countdown_timer_timeunit">' . sprintf( _n( "%d second,", "%d seconds,", "0", "go_countdown_timer" ), "0" ) . "</span> ";
			}	
			elseif($this->show_day)
			{
				$output = '<span class="go_countdown_timer_day go_countdown_timer_timeunit">' . sprintf( _n( "%d second,", "%d seconds,", "0", "go_countdown_timer" ), "0" ) . "</span> ";
			}	
			elseif($this->show_week)
			{
				$output = '<span class="go_countdown_timer_week go_countdown_timer_timeunit">' . sprintf( _n( "%d second,", "%d seconds,", "0", "go_countdown_timer" ), "0" ) . "</span> ";
			}
			elseif($this->show_month)
			{
				$output = '<span class="go_countdown_timer_month go_countdown_timer_timeunit">' . sprintf( _n( "%d second,", "%d seconds,", "0", "go_countdown_timer" ), "0" ) . "</span> ";
			}
			else
			{
				$output = '<span class="go_countdown_timer_second go_countdown_timer_timeunit">' . sprintf( _n( "%d second,", "%d seconds,", "0", "go_countdown_timer"), "0" ) . "</span> ";
			} 
		}
		return preg_replace("/(, ?<\/span> *)$/is", "</span>", $output 
		);
	}
	
} // end class

class get_delta_time{
		
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