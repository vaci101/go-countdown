<?php
/*********************************************************************************************************************\
* LAST UPDATE
* ============
* January 31, 2013
*
*
* AUTHOR
* =============
* Copyright 2013 Giga Omni Media (GigaOM.com)
* 
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
*  at your option) any later version.

* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.

* You should have received a copy of the GNU General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
* 
* FOR DOCUMENTATION VISIT https://github.com/fergbrain/countdown-timer.git
*
*
* PURPOSE
* =============
* Create a simple countdown timer utilizing a shortcode. This timer will only output the number of days until an event. 
* Additional updates must be performed to activate the other time segment
*
*
* SHORTCODE USAGE
* =============
* [go_countdown date='2013-03-01 08:00']
*
*
\*********************************************************************************************************************/
class GO_Countdown
{
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
		//wp_register_script('webkit-sprintf', plugins_url(dirname(plugin_basename(__FILE__)) . "/js/" . 'webtoolkit.sprintf.js'),  array('jquery'), 3);
		//wp_enqueue_script( 'webkit-sprintf');
	}

	/**
	 * WordPress shortcode handler.  Populate class variables with
	 * attributes
	 * @param array $atts
	 * @return string
	 */
	public function shortcode( $atts )
	{
		global $go_countdown_config;
		
		extract( shortcode_atts( array(
					'date' => '2013-03-15 08:00',
					'format' => 'Y m d h:m:s',
				), $atts ) );
		
		$target_date = new DateTime( $date, new DateTimeZone( $go_countdown_config[ 'go-datetimezone' ] ) );
		$current_date_time = new DateTime( 'now', new DateTimeZone( $go_countdown_config[ 'go-datetimezone' ] ) );

		$diff = abs( $target_date->getTimestamp() - $current_date_time->getTimestamp() );

		//write out time to hidden field
		echo "<input id='go_countdown_target_date' type='hidden' value='" . $target_date->getTimestamp() . "' />";
		echo "<input id='go_countdown_current_date' type='hidden' value='" . $current_date_time->getTimestamp() . "' />";
		
		//output timer
;		return "<div class='go_countdown_event'>" . $this->process_date( $target_date->getTimestamp(), $current_date_time->getTimestamp() ) . "</div>";
		
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
			
		$time_delta = new GO_Getdelta( $target_time, $now_time );

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
				$output = '<span class="go_countdown_year go_countdown_timeunit">' . sprintf( _n( "%d year,", "%d years,", $time_delta->year, "go_countdown" ), $time_delta->year ) . "</span> ";
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
				$output .= '<span class="go_countdown_month go_countdown_timeunit">' . sprintf(  _n( "%d month,", "%d months,", $time_delta->month, "go_countdown" ), $time_delta->month )."</span> ";
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
				$output .= '<span class="go_countdown_week go_countdown_timeunit">' . sprintf( _n( "%d week,", "%d weeks,", ( intval( ( $time_delta->day + intval($rollover/86400) )/7)), "go_countdown" ), ( intval ( ( $time_delta->day + intval( $rollover / 86400 ) ) / 7 )))."</span> ";		
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
				$output .= '<span class="go_countdown_day go_countdown_timeunit">' . sprintf( _n( "%d day,", "%d days,",  $time_delta->day, "go_countdown" ), $time_delta->day ) . "</span> ";
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
				$output .= '<span class="go_countdown_hour go_countdown_timeunit">' . sprintf( _n( "%d hour,", "%d hours,", $time_delta->hour, "go_countdown" ), $time_delta->hour ) . "</span> ";
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
				$output .= '<span class="go_countdown_minute go_countdown_timeunit">' . sprintf(  _n( "%d minute,", "%d minutes,", $time_delta->iminute, "go_countdown" ), $time_delta->iminute ) . "</span> ";
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
			$output .= '<span class="go_countdown_second go_countdown_timeunit">' . sprintf( _n( "%d second,", "%d seconds,", $time_delta->seconds, "go_countdown" ), $time_delta->seconds) . "</span> ";
		}
		
		//Catch blank statements
		if( $output == "" )
		{
			if($this->show_second)
			{
				$output = '<span class="go_countdown_second go_countdown_timeunit">' . sprintf( _n( "%d second,", "%d seconds,", "0", "go_countdown" ), "0" ) . "</span> ";
			}
			elseif($this->show_minute)
			{
				$output = '<span class="go_countdown_minute go_countdown_timeunit">' . sprintf( _n( "%d second,", "%d seconds,", "0", "go_countdown" ), "0" ) . "</span> ";
			}
			elseif($this->show_hour)
			{
				$output = '<span class="go_countdown_hour go_countdown_timeunit">' . sprintf( _n( "%d second,", "%d seconds,", "0", "go_countdown" ), "0" ) . "</span> ";
			}	
			elseif($this->show_day)
			{
				$output = '<span class="go_countdown_day go_countdown_timeunit">' . sprintf( _n( "%d second,", "%d seconds,", "0", "go_countdown" ), "0" ) . "</span> ";
			}	
			elseif($this->show_week)
			{
				$output = '<span class="go_countdown_week go_countdown_timeunit">' . sprintf( _n( "%d second,", "%d seconds,", "0", "go_countdown" ), "0" ) . "</span> ";
			}
			elseif($this->show_month)
			{
				$output = '<span class="go_countdown_month go_countdown_timeunit">' . sprintf( _n( "%d second,", "%d seconds,", "0", "go_countdown" ), "0" ) . "</span> ";
			}
			else
			{
				$output = '<span class="go_countdown_second go_countdown_timeunit">' . sprintf( _n( "%d second,", "%d seconds,", "0", "go_countdown"), "0" ) . "</span> ";
			} 
		}
		return preg_replace("/(, ?<\/span> *)$/is", "</span>", $output 
		);
	}
	
} // end class