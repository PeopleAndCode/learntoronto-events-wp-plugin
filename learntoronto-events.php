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

$learntoronto = new LearnToronto;
$events = $learntoronto->events();

if($events){
  foreach($events as $event) {
    $event_query = $learntoronto->get_event($event);
    if($event_query->have_posts()):
      // $event_query->the_post(); // getting errors non-object...
    else:
      $learntoronto->create_event($event);
    endif;
  }  
}


?>