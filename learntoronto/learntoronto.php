<?php
  
  class LearnToronto {
    $baseurl = "http://learntoronto.org";
    function events(){
      $json = file_get_contents($baseurl . "/v1/api/events.json");
      $obj = json_decode($json, true);  
      return $obj;
    }
  }
  
?>