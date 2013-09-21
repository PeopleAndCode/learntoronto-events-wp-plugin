<?php

// load custom metaboxes helpers
if(!class_exists('PCMetaboxes')){
  include_once(dirname(__FILE__).'/libs/pandc_metaboxes.php');
}

$pandc_metaboxes;
$metaboxes = new PAndCMetaboxes();

add_action( 'add_meta_boxes', array($metaboxes, 'metaboxes_add'));  
add_action('save_post', array($metaboxes, 'metaboxes_update'));

// load custom post types
include_once('posttypes/learntoronto-events.php');

?>