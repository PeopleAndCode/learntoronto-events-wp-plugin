<?php
  
class LearnToronto {
  public $baseurl = "http://learntoronto.org";
  public $prefix = "learntoronto_event";

  function events(){
    $json = file_get_contents($this->baseurl . "/api/v1/events.json");
    $events = json_decode($json, true);  
    return $events['events'];
  }

  function create_event($event){
    date_default_timezone_set('America/Toronto');
    $slug = str_replace(" ", "-", $event['name']) . $event['id'];
    $post = array(
      'comment_status' => 'closed',
      'ping_status' => 'open',
      'post_name' => $slug,
      'post_status' => 'publish', 
      'post_title' => $event['name'],
      'post_type' => $this->prefix
    );  
    $post_id = wp_insert_post( $post, $wp_error);
    $this->update_event($post_id, $event);  
  }

  function update_event($post_id, $event){
    foreach($event as $key => $value) {
      if($key == 'description'){
        $post_data = array(
          'ID' => $post_id,
          'post_content' => $value
        );
        wp_update_post($post_data);
      }
      elseif($key == 'venue'){
        foreach($value as $address_key => $address_value){
          update_post_meta($post_id, $this->prefix . "_venue_" . $address_key, $address_value);
        }
        $full_address = $this->full_formatted_address($value);
        $api_address = $this->map_formatted_address($value);
        update_post_meta($post_id, $this->prefix . "_venue_full_address", $full_address);
        update_post_meta($post_id, $this->prefix . "_venue_map_api_address", $api_address);
      } 
      else {
        update_post_meta($post_id, $this->prefix . "_" . $key, $value);
      }
    }
    update_post_meta($post_id, $this->prefix . "_approved", true);
  }

  function get_event($event) {
    $event_id = $event['id'];
    $args = array(
      "post_type" => $this->prefix,
      "meta_key" => $this->prefix . "_id",
      "meta_value" => $event_id,
      "posts_per_page" => 1
    );
    $query = new WP_Query($args);
    return $query;
  }

  function event_updated($event) {
    $query = get_event($event);
    $updated = false;
    if($query->have_posts()){
      while($query->have_posts()){
        $query->the_post();
        $post_id = get_the_id();
        $updated_at = get_post_meta($post_id, 'learntoronto_event_updated_at', true);
        if($updated_at != $event['updated_at']){
          $updated = true;
        }
      }
    }
    return $updated;
  }

  function event_exists($event){
    $exists = false;
    $query = $this->get_event($event);
    if($query->have_posts()){
      $exists = $query;
    }
    return $exists; 
  }

  function map_formatted_address($venue){
    foreach ($venue as $key => $value) {
      if($value){
        if($key == "name"){
          unset($venue[$key]);
        } else {
          $venue[$key] = str_replace("#", "", $venue[$key]);
          $venue[$key] = str_replace(",", "", $venue[$key]);
          $venue[$key] = str_replace(".", "", $venue[$key]);
          $venue[$key] = str_replace(" ", "+", $venue[$key]);
        }
      }
    }
    $venue = array_filter($venue);
    if($venue){
      $venue = implode("+", $venue);
    }
    return $venue;
  }

  function full_formatted_address($venue){
    $venue = array_filter($venue);
    $venue = implode(", ", $venue);
    return $venue;
  }

  function check_new_events() {
    $events = $this->events();
    if($events){
      foreach($events as $event) {
        $event_id = $event['id'];
        query_posts("post_type=" . $this->prefix . "&meta_key=" . $this->prefix . "_id&meta_value=" . $event_id . "&posts_per_page=1");
        if(have_posts()):
          while(have_posts()):
            the_post();
            $post_id = get_the_ID();
            $updated_at = get_post_meta($post_id, $this->prefix . '_updated_at');
            if($event['updated_at'] != $updated_at){
              $this->update_event($post_id, $event);
            }
          endwhile;
        else:
          $this->create_event($event);
        endif;
      }  
    }
  }
}

?>