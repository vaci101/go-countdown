/*
**
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
*  at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*
*
**/
jQuery(document).ready(function( $ )
{
	var countdown_time_elements = new Array( 'years', 'months', 'days', 'hours', 'minutes', 'seconds' );
	try
	{
		time_elements = ( $( '#go_countdown_time_elements' ).val() ).split(",");
		if( $.isArray( time_elements ) )
		{
			var wrk_array = new Array();

			//determine the elements passed
			$.each( time_elements, function( index, value )
			{
				if( $.inArray( value, countdown_time_elements ) > -1 )		//user passed time element.
				{
					wrk_array[ index ] = value;
				}
			});

			//prepare to write out html based on time elements selected.
			go_append_time_html( wrk_array );
		}

	go_countdown( wrk_array );
	}
	catch( err )
	{
		return;
	}

	/*
	 * Code in the method was used originally by Kerry James in a
	 * plugin located here => URI: http://studio.bloafer.com/wordpress-plugins/countdown/
	 * This method will prepare the html output for the countdown timer.
	 *
	 */
	function go_append_time_html( element_array )
	{
		var wrk_years, wrk_months, wrk_days, wrk_hours, wrk_minutes, wrk_seconds;

		$.each( element_array, function( index, value )
		{
			$("#go-countdown").html("");

			switch( value )
			{
				case 'years':
					wrk_years = $("<span>").addClass("years")
					break;
				case 'months':
					wrk_months = $("<span>").addClass("months");
					break;
				case 'days':
					wrk_days = $("<span>").addClass("days");
					break;
				case 'hours':
					wrk_hours = $("<span>").addClass("hours");
					break;
				case 'minutes':
					wrk_minutes = $("<span>").addClass("minutes");
					break;
				case 'seconds':
					wrk_seconds = $("<span>").addClass("seconds");
					break;

			}

		});

		$("#go-countdown").html("").append( wrk_years ).append(" " ).append( wrk_months ).append(" " ).append( wrk_days ).append(" " ).append( wrk_hours ).append(" ").append( wrk_minutes ).append(" ").append( wrk_seconds );

	}// function go_get_countdown_html( element_array )

	/*
	 * Code in the method was used originally by Kerry James in a
	 * plugin located here => URI: http://studio.bloafer.com/wordpress-plugins/countdown/
	 * This method will calculate the time and output the time element value.
	 *
	 */
	function go_countdown( time_elements )
	{
		var target_date = new Date( parseInt( jQuery( '#go_countdown_target_date' ).val() ) * 1000 );
		var now_date = new Date();
		var years = months = days = hours = minutes = seconds = 0;

		time_diff = target_date.getTime() - now_date.getTime();

		if( time_diff < 0 )
		{
			$(".countdown-container.years").html("0");
			$(".countdown-container.months").html("0");
			$(".countdown-container.hours").html("0");
			$(".countdown-container.days").html("0");
			$(".countdown-container.minutes").html("0");
			$(".countdown-container.seconds").html("0");
		}
		else if( time_diff >= 0 )
		{
			time_diff = Math.floor(  time_diff / 1000 );
			$.each( time_elements, function( index, value )
			{
				switch( value )
				{
					case 'years':
						years = Math.floor( time_diff / 31536000 );
						time_diff = time_diff % 31536000;
						break;
					case 'months':
						months = Math.floor( time_diff / 2628000 );
						time_diff = time_diff % 2628000;
						break;
					case 'days':
						days = Math.floor( time_diff / 86400 );
						time_diff = time_diff % 86400;
						break;
					case 'hours':
						hours = Math.floor( time_diff / 3600 );
						time_diff = time_diff % 3600;
						break;
					case 'minutes':
						minutes = Math.floor( time_diff / 60 );
						time_diff = time_diff % 60;
						break;
					case 'seconds':
						seconds = Math.floor( time_diff );
						break;
				}
			});

			if( $(".countdown-container .years").html() != years )
			{
				$(".countdown-container .years").html( ( years > 1  || years <= 0 ) ? years + ' years' : years + 'year' );
			}

			if( $(".countdown-container .months").html() != months )
			{
				$(".countdown-container .months").html( ( months > 1 || months <= 0 ) ? months + ' months' : months + 'month'  );
			}

			if( $(".countdown-container .days").html() != days )
			{
				$(".countdown-container .days").html( ( days > 1  || days <= 0 ) ? days + ' days' : days + 'day'  );
			}

			if( $(".countdown-container .hours").html() != hours )
			{
				$(".countdown-container .hours").html( ( hours > 1  || hours <= 0 ) ? hours + ' hours' : hours + 'hour'  );
			}

			if( $(".countdown-container .minutes").html() != minutes )
			{
				$(".countdown-container .minutes").html( ( minutes > 1  || minutes <= 0 ) ? minutes + ' minutes' : minutes + 'minute' );
			}

			if( $(".countdown-container .seconds").html() != seconds )
			{
				$(".countdown-container .seconds").html( ( seconds > 1  || seconds <= 0 ) ? seconds + ' seconds' : seconds + 'second' );
			}
			window.setTimeout( function(){ go_countdown( time_elements )}, 1000 );
		}
	}//function go_countdown( time_elements )
});