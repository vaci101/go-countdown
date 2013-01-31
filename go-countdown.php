<?php
/*
Plugin Name: GigaOM Countdown Timer
Description: Shortcode to create a countdown timer.  This timer will only display a countdown in days.  In order to display years or months or weeks or any other time element, an update to the code base must occur.
Version: 1.0
Author: GigaOm
Author URI: http://gigaom.com/
*/

require __DIR__ . '/components/class-go-countdown.php';
require __DIR__ . '/components/config/go-countdown.php';

global $go_countdown;
$go_countdown = new GO_Countdown();