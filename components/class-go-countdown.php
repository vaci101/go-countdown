<?php
/***
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
* There are several parameters you can pass to this shortode.
* 	date 		- 	'2013-03-01 08:00' - must have this value in this format.  year-month-day hour:minutes
*					( 24 hour format instead of 12 hour ) - REQUIRED
*
*	timezone	-	need to include a time zone identifier ( i.e. America/Los_Angeles )	 - see http://www.iana.org/time-zones.
*					If no timezone is entered, the default will be America/Los_Angeles.
*
*	display		-	pass the time elements you would like to display on your site. These time elements
*					must be comma delimited if displaying multiple time elements.  If no display is entered,
*					the default will be 'years,months,days,hours,minutes,seconds'.
*					For Ex:  	if you only want to display the "hours" pass hours ONLY
*									[go_countdown date='2013-03-15 08:00' timezone='American/Los_Angeles' display='hours' ]
*							 	if you want to display the "hours and seconds", pass "hours,seconds".  So on and so on...
*									[go_countdown date='2013-03-15 08:00' timezone='American/Los_Angeles' display='hours,seconds' ]
*
*
*
*/
class GO_Countdown
{
	/**
	 * Initialize the variables, plugin and register hooks.
	 */
	public function __construct()
	{
		add_action( 'init', array( $this, 'initialize_script' ) );
		add_shortcode( 'go_countdown', array( $this, 'shortcode' ) );
	} // end __construct

	public function initialize_script()
	{
		//enqueue countdown timer js
		wp_register_script('go-countdown-timer', plugins_url( dirname( plugin_basename( __FILE__ ) ) . '/js/go-countdown.js' ), array('jquery'), 4 );
		wp_enqueue_script( 'go-countdown-timer');
	}// function initialize_script()

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
					'timezone' => 'America/Los_Angeles',
					'display' => 'years,months,days,hours,minutes,seconds',
				), $atts ) );

		$new_timezone = new DateTimeZone( $timezone );
		$target_date = new DateTime( $date, $new_timezone );
		$current_date_time = new DateTime( 'now', $new_timezone );

		$diff = abs( $target_date->getTimestamp() - $current_date_time->getTimestamp() );

		//write out time to hidden field
		echo "<input id='go_countdown_target_date' type='hidden' value='" . $target_date->getTimestamp() . "' />";

		//write out requested time elements to hidden field, will use in javascript later.
		echo "<input id='go_countdown_time_elements' type='hidden' value='" . strtolower( $display ) . "' />";

		//output timer
		return '<div id="go-countdown" class="countdown-container">' .  date('l jS \of F Y h:i:s A', $target_date->getTimestamp() ) . '</div>';


	} // end shortcode
}// end class