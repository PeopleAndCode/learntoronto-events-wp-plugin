<?php
/**
* @package LearnToronto_Events 
* @version 1.0
*/
/*
Plugin Name: LearnToronto Events Listings
Plugin URI: http://learntoronto.org/
Description: This plugin creates a Custom Post type that allows adding new events from the LearnToronto.org API feed (http://learntoronto.org/api/v1/events.json)
Author: Raymond Kao
Version: 1.0
Author URI: http://peopleandcode.com
*/

include_once('custom-post-types.php');
include_once('learntoronto/learntoronto.php');

register_activation_hook( __FILE__, 'learntoronto_event_daily_activation' );

add_action('learntoronto_event_daily_event', 'learntoronto_daily_event');

function learntoronto_event_daily_activation() {
  $learntoronto = new LearnToronto;
  $learntoronto->check_new_events();
  $time_zone = new DateTimeZone('America/Toronto');
  $new_zone = new DateTimeZone('UTC');

  $date = date('Y-m-d 4:00:00');
  $date_time = new DateTime($date, $time_zone);
  $date_time->setTimeZone($new_zone);
  
  $year = $date_time->format('Y');
  $day = $date_time->format('d');
  $month = $date_time->format('m');
  $hour = $date_time->format('H');
  $min = $date_time->format('i');
  $sec = $date_time->format('s');

  $time = mktime($hour, $min, $sec, $month, $day, $year);

  wp_schedule_event($time, 'daily', 'learntoronto_event_daily_event');
}

function learntoronto_daily_event() {
  $learntoronto = new LearnToronto;
  $learntoronto->check_new_events();
}

register_deactivation_hook( __FILE__, 'learntoronto_event_daily_deactivation');

function learntoronto_event_daily_deactivation() {
  wp_clear_scheduled_hook('learntoronto_event_daily_event');
}

?>