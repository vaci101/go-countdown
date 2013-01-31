jQuery(function ($) {
	function go_countdown_js()
	{
		// alert( "EHERE");
		$( ".go_countdown_event" ).each( function ( i ) 
		{
			var target_date = new Date( parseInt( $( '#go_countdown_target_date' ).val() ) *1000 );
			var now_date = new Date();
			console.log('Now date is ' + now_date );
			console.log('Target date is ' + target_date );
			
			
			if( ( target_date.getTime() - now_date.getTime() ) < 0 )
			{
				this.innerHTML = process_date( now_date, target_date );			
			}
			else if( ( target_date.getTime() - now_date.getTime() ) >= 0 )
			{				
				this.innerHTML = process_date( target_date, now_date );
			}
			
		});
	   
	    window.setTimeout( go_countdown_js, 1000 );
	}

	go_countdown_js();
		
	function process_date ( target_time, now_time ) 
	{
			
		var rollover = 0;
		var sig_num_hit = false;
		var total_time = 0;
	
		var now_date = now_time;
		var target_date = target_time;
		
		var  output = '';
		
		var now_year = now_date.getFullYear();
		var now_month = now_date.getMonth() + 1;
		var now_day = now_date.getDate();
		var now_hour = now_date.getHours();
		var now_minute = now_date.getMinutes();
		var now_second = now_date.getSeconds();
		
		var target_year = target_date.getFullYear();
		var target_month = target_date.getMonth() + 1;
		var target_day = target_date.getDate();
		var target_hour = target_date.getHours();
		var target_minute = target_date.getMinutes();
		var target_second = target_date.getSeconds();
		
		var resultant_year = target_year - now_year;
		var resultant_month = target_month - now_month;
		var resultant_day = target_day - now_day;
		var resultant_hour = target_hour - now_hour;
		var resultant_minute = target_minute - now_minute;
		var resultant_second = target_second - now_second;
	
		var show_year = false;
		var show_month = false;
		var show_week = false;
		var show_day = true;
		var show_hour = false;
		var show_minute = false;
		var show_second = false;
		var stripZero = false;
		
		if( resultant_second < 0 )
		{
			resultant_minute--;
			resultant_second = 60 + resultant_second;
		}
		
		if( resultant_minute < 0 )
		{
			resultant_hour--;
			resultant_minute = 60 + resultant_minute;
		}
		
		if( resultant_hour < 0 )
		{
			resultant_day--;
			resultant_hour = 24 + resultant_hour;
		}
		
		if( resultant_day < 0 )
		{
			resultant_month--;
			resultant_day = resultant_day + 32 - new Date(now_year, now_month-1, 32 ).getDate();
		}
		
		
	
		if( resultant_month < 0 )
		{
			resultant_year--;
			resultant_month = resultant_month + 12;
		}
	
		//Year
		if(  show_year )
		{
			if( sig_num_hit || !stripZero || resultant_year )
			{
				 output = ( '<span class="fergcorp_countdownTimer_year fergcorp_countdownTimer_timeUnit">' + resultant_year + ( resultant_year > 1 ) ? resultant_year + ' years ' : resultant_year + ' year '  + '</span> ' );
				
				sig_num_hit = true;
			}
		}
		else
		{
			rollover = resultant_year*31536000;
		}
	
		//Month	
		if(  show_month )
		{
			if( sig_num_hit || !stripZero || ( resultant_month + parseInt(rollover/2628000 ) ) ){
				console.log( "Resultant Month is " + resultant_month );
				resultant_month = resultant_month + parseInt( rollover/2628000 );
				console.log( "Resultant Month NOW is " + resultant_month );
				console.log( "OUTPUT IS " +  output );
				output = output + ( '<span class="go_countdown_month go_countdown_timeunit">' +  resultant_month + ( resultant_month > 1 ) ? resultant_month + ' months ' : resultant_month + ' month ' + '</span> ' );
				console.log( "OUTPUT NOW IS " + s );
				rollover = rollover - parseInt( rollover/2628000 ) * 2628000;
				sig_num_hit = true;
			}
		}
		else
		{
			//If we don't want to show months, let's just calculate the exact number of seconds left since all other units of time are fixed (i.e. months are not a fixed unit of time)		
			total_time = parseInt( target_time.getTime() - now_time.getTime() ) / 1000;
			
			//If we showed years, but not months, we need to account for those.
			if( parseInt( getOptions['show_year'] ))
			{
				total_time = total_time - resultant_year*31536000;
			}
				
			//Re calculate the resultant times
			resultant_week = 0;//parseInt( total_time/(86400*7) );
	 
			resultant_day = parseInt( total_time / 86400 );
	
			resultant_hour = parseInt( ( total_time - resultant_day * 86400 ) / 3600 );
			
			resultant_minute = parseInt( ( total_time - resultant_day * 86400 - resultant_hour * 3600 ) / 60 );
			
			resultant_second = parseInt( ( total_time - resultant_day * 86400 - resultant_hour * 3600 - resultant_minute * 60 ) );
			
			//and clear any rollover time
			rollover = 0;
	
		}
		
		//Week (weeks are counted differently becuase we can just take 7 days and call it a week...so we do that)
		/*if(  show_week ){
			if( sig_num_hit || !stripZero || parseInt( (resultant_day + parseInt(rollover/86400) )/7)){
				resultant_day = resultant_day + parseInt(rollover/86400);
				output = output + '<span class="fergcorp_countdownTimer_week fergcorp_countdownTimer_timeUnit">' + sprintf(_n(fergcorp_countdown_timer_js_lang.week, fergcorp_countdown_timer_js_lang.weeks, (parseInt( (resultant_day + parseInt(rollover/86400) )/7))), (parseInt( (resultant_day + parseInt(rollover/86400) )/7))) + '</span> ';
				rollover = rollover - parseInt(rollover/86400)*86400;
				resultant_day = resultant_day - parseInt( (resultant_day + parseInt(rollover/86400) )/7 )*7;
				sig_num_hit = true;
			}
		}
		*/
		//Day
		if(  show_day )
		{
			if( sig_num_hit || !stripZero || (resultant_day + parseInt( rollover / 86400  ) ) )
			{
				
				console.log( "Resultant DAY is " + resultant_day );
				resultant_day = resultant_day + parseInt( rollover / 86400 );
				console.log( "Resultant DAY NOW is " + resultant_day );
				
				output = output + ( '<span class="go_countdown_day go_countdown_timeunit">' + resultant_day + ( resultant_day > 1 ) ? resultant_day + ' days ' : resultant_day + ' day ' + '</span> ' );
				
				rollover = rollover - parseInt( rollover / 86400 ) * 86400;
				sig_num_hit = true;
			}
		}
		else
		{
			rollover = rollover + resultant_day * 86400;
		}
		
		//Hour
		if(  show_hour )
		{
			if( sig_num_hit || !stripZero || ( resultant_hour + parseInt( rollover / 3600 ) ) )
			{
				resultant_hour = resultant_hour + parseInt(rollover/3600);
				output =  output + ( '<span class="go_countdown_hour go_countdown_timeunit">' + resultant_hour + ( resultant_hour > 1 ) ? resultant_hour + ' hours ' : resultant_hour + ' hour ' + '</span> ' );
				rollover = rollover - parseInt( rollover / 3600 ) * 3600;
				sig_num_hit = true;
			}
		}
		else
		{
			rollover = rollover + resultant_hour * 3600;
		}
		
		//Minute
		if(  show_minute )
		{
			if( sig_num_hit || !stripZero || ( resultant_minute + parseInt( rollover / 60 ) ) )
			{
				resultant_minute = resultant_minute + parseInt( rollover / 60);
				 output =  output + ( '<span class="go_countdown_minute go_countdown_timeunit">' + resultant_minute + ( resultant_minute > 1 ) ? resultant_minute + ' minutes ' : resultant_minute + ' minute '  + '</span> ' );
				rollover = rollover - parseInt( rollover / 60 ) * 60;
				sig_num_hit = true;
			}
		}
		else
		{
			rollover = rollover + resultant_minute * 60;
		}
		
		//Second
		if(  show_second ) 
		{
			resultant_second = resultant_second + rollover;
			 output =  output + ( '<span class="go_countdown_second go_countdown_timeunit">' + resultant_second + ( resultant_second > 1 ) ? resultant_second + ' seconds ' : resultant_second + ' second '   + '</span> ' );
		}
		
		
		//Catch blank statements
		/*if( output==''){
			if( parseInt( show_second )){
				 output = '<span class="go_countdown_second go_countdown_timeUnit">' + sprintf(fergcorp_countdown_timer_js_lang.seconds, 0) + '</span> ';
			}
			else if( parseInt( getOptions['show_minute'] )){
				 output = '<span class="go_countdown_minute go_countdown_timeUnit">' + sprintf(fergcorp_countdown_timer_js_lang.minutes, 0) + '</span> ';
			}
			else if( parseInt( getOptions['show_hour'] )){
				 output = '<span class="go_countdown_hour go_countdown_timeUnit">' + sprintf(fergcorp_countdown_timer_js_lang.hours, 0) + '</span> ';
			}	
			else if( parseInt( getOptions['show_day'] )){
				 output = '<span class="go_countdown_day go_countdown_timeUnit">' + sprintf(fergcorp_countdown_timer_js_lang.days, 0) + '</span> ';
			}
			else if( parseInt( getOptions['show_week'] )){
				 output = '<span class="go_countdown_week go_countdown_timeUnit">' + sprintf(fergcorp_countdown_timer_js_lang.weeks, 0) + '</span> ';
			}
			else if( parseInt( getOptions['show_month'] )){
				 output = '<span class="go_countdown_month go_countdown_timeUnit">' + sprintf(fergcorp_countdown_timer_js_lang.months, 0) + '</span> ';
			}
			else{
				 output = '<span class="go_countdown_year go_countdown_timeUnit">' + sprintf(fergcorp_countdown_timer_js_lang.years, 0) + '</span> ';
			}
		}*/
	
		
		return s.replace(/(, ?<\/span> *)$/, "<\/span>"); //...and return the result (a string)
	}
	
});

