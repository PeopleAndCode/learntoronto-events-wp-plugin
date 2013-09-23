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
    
    $values["formatted_address"] = formatted_address($values);

    $values["query_address"] = query_address($values);

    return $values;
  }
  return false;
}

function formatted_address($values) {
  $formatted_address = "";
  
  if($values["location_name"]) {
    $formatted_address .= $values["location_name"] . "<br/>";
  }

  if($values["address1"]) {
    $formatted_address .= $values["address1"] . "<br/>";
  }

  if($values["address2"]) {
    $formatted_address .= $values["address2"] . "<br/>";
  }

  if($values["address3"]) {
    $formatted_address .= $values["address3"] . "<br/>";
  }

  if(($values["city"]) && ($values["province"])) {
    $formatted_address .= $values["city"] . ", " . $values["province"];

  } else if ($values["city"]) {
      $formatted_address .= $values["city"];

  } else if ($values["province"]) {
      $formatted_address .= $values["province"];
  }

  return $formatted_address;
}

function query_address($values) {
  $query_address = "";

  foreach($values as $key => $value) {
    $values[$key] = str_replace(" ", "+", $value);
  }

  if($values["address1"]) {
    $query_address .= $values["address1"];
  }

  if($values["address2"]) {
    $query_address .= "+" . $values["address2"];
  }

  if($values["address3"]) {
    $query_address .= "+" . $values["address3"];
  }

  if(($values["city"]) && ($values["province"])) {
    $query_address .= "+" . $values["city"] . "+" . $values["province"];

  } else if ($values["city"]) {
      $query_address .= "+" . $values["city"];

  } else if ($values["province"]) {
      $query_address .= "+" . $values["province"];
  }

  return $query_address; 
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
        'name' => 'Name:',
        'desc' => 'e.g. August Ember.js Meetup',
        'id' => 'learntoronto_event_name',
        'type' => 'text',
        'default' => ''
      ),
      array(
        'name' => 'Location Name:',
        'desc' => 'e.g. People And Code',
        'id' => 'learntoronto_event_location_name',
        'type' => 'text',
        'default' => ''
      ),
      array(
        'name' => 'Street Number and Name:',
        'desc' => 'e.g. 26 Soho Street',
        'id' => 'learntoronto_event_address1',
        'type' => 'text',
        'default' => ''
      ),
      array(
        'name' => 'Address Line 2:',
        'desc' => 'e.g. Unit 350',
        'id' => 'learntoronto_event_address2',
        'type' => 'text',
        'default' => ''
      ),
      array(
        'name' => 'Address Line 3:',
        'id' => 'learntoronto_event_address3',
        'type' => 'text',
        'default' => ''
      ),
      array(
        'name'    => 'City:',
        'desc'    => 'e.g. Toronto',
        'id'      => 'learntoronto_event_city',
        'type'    => 'text'
      ),
      array(
        'name'    => 'Province:',
        'desc'    => 'e.g. Ontario',
        'id'      => 'learntoronto_event_province',
        'type'    => 'text'
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
        'id' => 'learntoronto_event_date',
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