<?php
// initialize the custom post type
add_action( 'init', 'create_event_post_type', 0 );

function create_event_post_type() {
  // The various labels and button text for the custom post type
  $labels =   array(
    'name' => __( 'Events' ),
    'singular_name' => __( 'Events' ),
    'add_new' => 'Add Event',
    'add_new_item' => 'Add Event',
    'edit_item' => __('Edit Event'),
    'new_item' => 'Event',
    'view_item' => 'View Event',
    'search_items' => 'Search Events',
    'not_found' => 'No Events found',
    'not_found_in_trash' => 'No Events found in Trash'
  );

  // The arguments array for creating the Event custom post type
  $args =   array( 
    'labels' => $labels, 
    'public' => true, 
    'publicly_queryable' => true, 
    'show_ui' => true, 
    'query_var' => true, 
    'rewrite' => array('slug' => 'event', 'with_front' => false),
    'capability_type' => 'post', 
    'hierarchical' => false, 
    'menu_position' => 5, 
    'taxonomies' => array('category'),
    'supports' => array('title', 'thumbnail')
  ); 

  // this function registers and actually creates the custom post type
  register_post_type('learntoronto_event', $args);
}

function event_info(){
  $meta = get_post_meta(get_the_ID());
  $values = array();
  if($meta){
    foreach($meta as $key => $value){
      $key = str_replace("learntoronto_event_", "" , $key);
      $values[$key] = $value[0];
    }

    if(!array_key_exists("paid", $values)){
      $values["paid"] = "Free";
    }
    
    // $values["formatted_address"] = formatted_address($values);

    // $values["query_address"] = query_address($values);

    return $values;
  }
  return false;
}

$pandc_metaboxes['learntoronto_event'] = array(
  array(
    'id' => 'pc_event_details',
    'title' => 'Event Details',
    'context' => 'normal',
    'priority' => 'high',
    'fields' => array(
      array(
        'name' => 'Approved',
        'desc' => 'e.g. August Ember.js Meetup',
        'id' => 'learntoronto_event_approved',
        'type' => 'checkbox',
        'default' => '0'
      ),
      array(
        'name' => 'Location Name:',
        'desc' => 'e.g. People And Code',
        'id' => 'learntoronto_event_venue_name',
        'type' => 'text',
        'default' => ''
      ),
      array(
        'name' => 'Street Number and Name:',
        'desc' => 'e.g. 26 Soho Street',
        'id' => 'learntoronto_event_venue_address_1',
        'type' => 'text',
        'default' => ''
      ),
      array(
        'name' => 'Address 2:',
        'id' => 'learntoronto_event_venue_address_2',
        'type' => 'text',
        'default' => ''
      ),
      array(
        'name' => 'City:',
        'desc' => 'e.g. Toronto',
        'id' => 'learntoronto_event_venue_city',
        'type' => 'text',
        'default' => ''
      ),
      array(
        'name' => 'Province:',
        'desc' => 'e.g. Ontario',
        'id' => 'learntoronto_event_venue_state',
        'type' => 'text',
        'default' => ''
      ),
      array(
        'name' => 'Country:',
        'desc' => 'e.g. Canada',
        'id' => 'learntoronto_event_venue_country',
        'type' => 'text',
        'default' => ''
      ),
      array(
        'name' => 'Type:',
        'desc' => 'e.g. Meetup/Workshop/Social etc.',
        'id' => 'learntoronto_event_type',
        'type' => 'text',
        'default' => 'Meetup'
      ),
      array(
        'name' => 'Event Date:',
        'desc' => 'eg. Aug 20th, 2013 @7:30pm',
        'id' => 'learntoronto_event_start_time',
        'type' => 'text',
        'default' => ''
      ),
      array(
        'name' => 'Paid Event?',
        'desc' => 'Is this a paid event?',
        'id' => 'learntoronto_event_paid',
        'type' => 'radio',
        'options' => array(
          array(
            'name' => 'Paid',
            'value' => 'Paid'
          ),
          array(
            'name' => 'Free',
            'value' => 'Free'
          ),
        )
      ),
      array(
        'name' => 'Event URL:',
        'desc' => 'e.g. http://www.meetup.com/Mobile-Startups-TO/events/104501262/',
        'id' => 'learntoronto_event_url',
        'type' => 'text',
        'default' => ''
      )
    )
  )
);

?>