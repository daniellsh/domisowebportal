<?php
require 'config.php';


// Connect to database
$conn = new mysqli($db_host, $db_username, $db_password, $db_name);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

if (!function_exists('chunkArrayByKeyValue')) {
  function chunkArrayByKeyValue($array, $key)
  {

    $chunks = array_reduce($array, function ($result, $item) use ($key) {
      $value = $item[$key];
      if (!isset($result[$value])) {
        $result[$value] = array();
      }
      $result[$value][] = $item;
      return $result;
    }, array());
    return $chunks;
  }
}
