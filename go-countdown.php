<?php
/*
Plugin Name: GigaOM Countdown Timer
Description: Shortcode to create a countdown timer.  This timer will only display a countdown in days.  In order to display years or months or weeks or any other time element, an update to the code base must occur.
Version: 1.0
Author: GigaOm
Author URI: http://gigaom.com/
License: GPL2
*/

/*
Copyright 2013 Giga Omni Media (GigaOM.com)

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/


require __DIR__ . '/components/class-go-countdown.php';
require __DIR__ . '/components/config/go-countdown.php';

global $go_countdown;
$go_countdown = new GO_Countdown();