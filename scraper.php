<?php

$feed_source = "http://api.citybik.es/citibikenyc.json";

// put data into carto
$feed = file_get_contents($feed_source);

$result = json_decode($feed);



foreach($result as $key => $value) {
    if($value) {
      
    $lat = ($value->lat)/1000000;
    $lng = ($value->lng)/1000000;
    
    $db = "INSERT INTO docks (id, name, number, last, lat, lng, idx, free, bikes) VALUES ("
      . "$value->id,"
      . "'$value->name',"
      . "$value->number,"
      . "'$value->timestamp',"
      . "$lat,$lng,"
      . "$value->idx,"
      . "$value->free,"
      . "$value->bikes)";

        echo $value->name . " " . carto($db) . "\n";
        
    }
}

function carto($query, $geojson) {
  
  // run any cartodb query

  include('api.php');
  
  $api_key = $cartoapi;
  
  $carto_root = 'http://fkh.cartodb.com/api/v2/sql?';
  
  if ($geojson == TRUE) {
    $format = "format=GeoJSON&";
  }
  
  $sql_query = urlencode($query);

  $carto_url = $carto_root . $format . "q=" . $sql_query . "&api_key=" . $api_key ;
  
  // doing the curl...
  $ch = curl_init();
  
  curl_setopt($ch, CURLOPT_URL, $carto_url);
  curl_setopt($ch, CURLOPT_HEADER, FALSE);
  curl_setopt($ch, CURLOPT_VERBOSE, FALSE);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    
  $carto_result = curl_exec($ch);
  
  // close cURL resource, and free up system resources
  curl_close($ch);

  return $carto_result;
  
}

// construct a url to insert a point into the db

function addToDb($name, $block) {

  $timestamp = time();
  
  $sql = "INSERT INTO NAMES (neighborhood, block, timestamp) VALUES ('{$name}', {$block}, {$timestamp})";
  
  carto($sql, FALSE);
  
}

?>
