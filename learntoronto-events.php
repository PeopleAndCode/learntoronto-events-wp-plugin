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

include_once('custom_post_types.php');
include_once('learntoronto/learntoronto.php');

add_theme_support( 'post-thumbnails' );

?>