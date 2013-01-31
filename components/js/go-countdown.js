jQuery(function ($) {
	function go_countdown_js()
	{
		// alert( "EHERE");
		$(".go_countdown_event").each(function (i) {

			var targetDate = new Date( parseInt( $( '#go_countdown_target_date' ).val() ) *1000 );
			var nowDate = new Date();
			console.log('Now date is ' + nowDate );
			console.log('Target date is ' + targetDate );
			
			
			if(( targetDate.getTime() - nowDate .getTime() ) < 0 )
			{
				this.innerHTML = process_date( nowDate, targetDate );			
			}
			else if(( targetDate.getTime() - nowDate .getTime() ) >= 0 )
			{
				
				this.innerHTML = process_date( targetDate, nowDate );
			}
			
		});
	   
	    window.setTimeout( go_countdown_js, 1000);
	}

	go_countdown_js();
		
	function process_date ( targetTime, nowTime ) 
	{
			
		var rollover = 0;
		var sigNumHit = false;
		var totalTime = 0;
	
		var nowDate = nowTime;
		var targetDate = targetTime;
		
		var s = '';
		
		var nowYear = nowDate.getFullYear();
		var nowMonth = nowDate.getMonth() + 1;
		var nowDay = nowDate.getDate();
		var nowHour = nowDate.getHours();
		var nowMinute = nowDate.getMinutes();
		var nowSecond = nowDate.getSeconds();
		
		var targetYear = targetDate.getFullYear();
		var targetMonth = targetDate.getMonth() + 1;
		var targetDay = targetDate.getDate();
		var targetHour = targetDate.getHours();
		var targetMinute = targetDate.getMinutes();
		var targetSecond = targetDate.getSeconds();
		
		var resultantYear = targetYear - nowYear;
		var resultantMonth = targetMonth - nowMonth;
		var resultantDay = targetDay - nowDay;
		var resultantHour = targetHour - nowHour;
		var resultantMinute = targetMinute - nowMinute;
		var resultantSecond = targetSecond - nowSecond;
	
		var showYear = false;
		var showMonth = false;
		var showWeek = false;
		var showDay = true;
		var showHour = false;
		var showMinute = false;
		var showSecond = false;
		var stripZero = false;
		
		if(resultantSecond < 0){
			resultantMinute--;
			resultantSecond = 60 + resultantSecond;
		}
		
		if(resultantMinute < 0){
			resultantHour--;
			resultantMinute = 60 + resultantMinute;
		}
		
		if(resultantHour < 0){
			resultantDay--;
			resultantHour = 24 + resultantHour;
		}
		
		if(resultantDay < 0){
			resultantMonth--;
			resultantDay = resultantDay + 32 - new Date(nowYear, nowMonth-1, 32).getDate();
		}
		
		
	
		if(resultantMonth < 0){
			resultantYear--;
			resultantMonth = resultantMonth + 12;
		}
	
		//Year
		if( showYear ){
			if(sigNumHit || !stripZero || resultantYear){
				s = '<span class="fergcorp_countdownTimer_year fergcorp_countdownTimer_timeUnit">' + resultantYear + ( resultantYear > 1 ) ? resultantYear + ' years ' : resultantYear + ' year '  + '</span> ';
				
				sigNumHit = true;
			}
		}
		else{
			rollover = resultantYear*31536000;
		}
	
		//Month	
		if( showMonth ){
			if(sigNumHit || !stripZero || (resultantMonth + parseInt(rollover/2628000)) ){
				console.log( "Resultant Month is " + resultantMonth );
				resultantMonth = resultantMonth + parseInt(rollover/2628000);
				console.log( "Resultant Month NOW is " + resultantMonth );
				console.log( "OUTPUT IS " + s );
				s = s + ( '<span class="go_countdown_month go_countdown_timeunit">' +  resultantMonth + ( resultantMonth > 1 ) ? resultantMonth + ' months ' : resultantMonth + ' month ' + '</span> ' );
				console.log( "OUTPUT NOW IS " + s );
				rollover = rollover - parseInt(rollover/2628000)*2628000;
				sigNumHit = true;
			}
		}
		else{
			//If we don't want to show months, let's just calculate the exact number of seconds left since all other units of time are fixed (i.e. months are not a fixed unit of time)		
			totalTime = parseInt(targetTime.getTime() - nowTime.getTime())/1000;
			
			//If we showed years, but not months, we need to account for those.
			if(parseInt( getOptions['showYear'] )){
				totalTime = totalTime - resultantYear*31536000;
			}
				
			//Re calculate the resultant times
			resultantWeek = 0;//parseInt( totalTime/(86400*7) );
	 
			resultantDay = parseInt( totalTime/86400 );
	
			resultantHour = parseInt( (totalTime - resultantDay*86400)/3600 );
			
			resultantMinute = parseInt( (totalTime - resultantDay*86400 - resultantHour*3600)/60 );
			
			resultantSecond = parseInt( (totalTime - resultantDay*86400 - resultantHour*3600 - resultantMinute*60) );
			
			//and clear any rollover time
			rollover = 0;
	
		}
		
		//Week (weeks are counted differently becuase we can just take 7 days and call it a week...so we do that)
		/*if( showWeek ){
			if(sigNumHit || !stripZero || parseInt( (resultantDay + parseInt(rollover/86400) )/7)){
				resultantDay = resultantDay + parseInt(rollover/86400);
				s = s + '<span class="fergcorp_countdownTimer_week fergcorp_countdownTimer_timeUnit">' + sprintf(_n(fergcorp_countdown_timer_js_lang.week, fergcorp_countdown_timer_js_lang.weeks, (parseInt( (resultantDay + parseInt(rollover/86400) )/7))), (parseInt( (resultantDay + parseInt(rollover/86400) )/7))) + '</span> ';
				rollover = rollover - parseInt(rollover/86400)*86400;
				resultantDay = resultantDay - parseInt( (resultantDay + parseInt(rollover/86400) )/7 )*7;
				sigNumHit = true;
			}
		}
		*/
		//Day
		if( showDay ){
			if(sigNumHit || !stripZero || (resultantDay + parseInt(rollover/86400)) ){
				
				console.log( "Resultant DAY is " + resultantDay );
				resultantDay = resultantDay + parseInt(rollover/86400);
				console.log( "Resultant DAY NOW is " + resultantDay );
				
				s = s + ( '<span class="go_countdown_day go_countdown_timeunit">' + resultantDay + ( resultantDay > 1 ) ? resultantDay + ' days ' : resultantDay + ' day ' + '</span> ' );
				
				rollover = rollover - parseInt(rollover/86400)*86400;
				sigNumHit = true;
			}
		}
		else{
			rollover = rollover + resultantDay*86400;
		}
		
		//Hour
		if( showHour ){
			if(sigNumHit || !stripZero || (resultantHour + parseInt(rollover/3600)) ){
				resultantHour = resultantHour + parseInt(rollover/3600);
				s = s + ( '<span class="go_countdown_hour go_countdown_timeunit">' + resultantHour + ( resultantHour > 1 ) ? resultantHour + ' hours ' : resultantHour + ' hour ' + '</span> ' );
				rollover = rollover - parseInt(rollover/3600)*3600;
				sigNumHit = true;
			}
		}
		else{
			rollover = rollover + resultantHour*3600;
		}
		
		//Minute
		if( showMinute ){
			if(sigNumHit || !stripZero || (resultantMinute + parseInt(rollover/60)) ){
				resultantMinute = resultantMinute + parseInt(rollover/60);
				s = s + ( '<span class="go_countdown_minute go_countdown_timeunit">' + resultantMinute + ( resultantMinute > 1 ) ? resultantMinute + ' minutes ' : resultantMinute + ' minute '  + '</span> ' );
				rollover = rollover - parseInt(rollover/60)*60;
				sigNumHit = true;
			}
		}
		else{
			rollover = rollover + resultantMinute*60;
		}
		
		//Second
		if( showSecond ) {
			resultantSecond = resultantSecond + rollover;
			s = s + ( '<span class="go_countdown_second go_countdown_timeunit">' + resultantSecond + ( resultantSecond > 1 ) ? resultantSecond + ' seconds ' : resultantSecond + ' second '   + '</span> ' );
		}
		
		
		//Catch blank statements
		/*if(s==''){
			if(parseInt( getOptions['showSecond'] )){
				s = '<span class="fergcorp_countdownTimer_second fergcorp_countdownTimer_timeUnit">' + sprintf(fergcorp_countdown_timer_js_lang.seconds, 0) + '</span> ';
			}
			else if(parseInt( getOptions['showMinute'] )){
				s = '<span class="fergcorp_countdownTimer_minute fergcorp_countdownTimer_timeUnit">' + sprintf(fergcorp_countdown_timer_js_lang.minutes, 0) + '</span> ';
			}
			else if(parseInt( getOptions['showHour'] )){
				s = '<span class="fergcorp_countdownTimer_hour fergcorp_countdownTimer_timeUnit">' + sprintf(fergcorp_countdown_timer_js_lang.hours, 0) + '</span> ';
			}	
			else if(parseInt( getOptions['showDay'] )){
				s = '<span class="fergcorp_countdownTimer_day fergcorp_countdownTimer_timeUnit">' + sprintf(fergcorp_countdown_timer_js_lang.days, 0) + '</span> ';
			}
			else if(parseInt( getOptions['showWeek'] )){
				s = '<span class="fergcorp_countdownTimer_week fergcorp_countdownTimer_timeUnit">' + sprintf(fergcorp_countdown_timer_js_lang.weeks, 0) + '</span> ';
			}
			else if(parseInt( getOptions['showMonth'] )){
				s = '<span class="fergcorp_countdownTimer_month fergcorp_countdownTimer_timeUnit">' + sprintf(fergcorp_countdown_timer_js_lang.months, 0) + '</span> ';
			}
			else{
				s = '<span class="fergcorp_countdownTimer_year fergcorp_countdownTimer_timeUnit">' + sprintf(fergcorp_countdown_timer_js_lang.years, 0) + '</span> ';
			}
		}*/
	
		
		return s.replace(/(, ?<\/span> *)$/, "<\/span>"); //...and return the result (a string)
	}
	
});

