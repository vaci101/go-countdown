=== go-countdown ===

Contributors: opine
Tags: countdown, countdown-timer,timer,shortcode
Requires at least: 3.5
Tested up to: 3.5
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

This plugin allows you to setup a countdown timer using a shortcode.

== Description ==

This plugin allows you to setup a countdown timer using a shortcode.  Currently only days display.
To use the shortcode [go_countdown date = '2013-03-01 08:00' ]

== Installation ==

1. Download the plugin and unzip it into wp-content/plugins.
2. Activate the plugin by going to Plugins and choosing Activate.
3. Plugin is not active.
4. Go to your post and add your shortcode. Shortcode examples follow.

== Shortcode Usage ==
== DEFAULT ==

1. [go_countdown date='2013-03-15 08:00'] - ( replace with the date of your upcoming event )

There are several parameters you can pass to this shortode. 

1. date 	- 	'2013-03-01 08:00' - must have this value in this format.  year-month-day hour:minutes 
			( 24 hour format instead of 12 hour ) - REQUIRED
2. timezone	-	need to include a time zone identifier ( i.e. America/Los_Angeles )
			see http://www.iana.org/time-zones.  If no timezone is entered, the default will be America/Los_Angeles.
3. display	-	pass the time elements you would like to display on your site. These time elements
			must be comma delimited if displaying multiple time elements.  If no display is entered,
			the default will be 'years,months,days,hours,minutes,seconds'.
			For Ex:	if you only want to display the "hours" pass hours ONLY
					[go_countdown date='2013-03-15 08:00' timezone='American/Los_Angeles' display='hours' ]
				if you want to display the "hours and seconds", pass "hours,seconds".  So on and so on...
					[go_countdown date='2013-03-15 08:00' timezone='American/Los_Angeles' display='hours,seconds' ]   
