<?php 

/*
 * Create a singleton call of the Go_Countdown plugin.  
 * */
function go_countdown()
{
	global $go_countdowns;

	if ( ! $go_countdown )
	{
		$go_countdown = new GO_Countdown;
	}//end if

	return $go_countdown;
}//end go_countdown