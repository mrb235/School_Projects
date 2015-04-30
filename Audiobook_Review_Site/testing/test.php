<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$test_json = array(
  'username' => $_POST['username'],
  'password' => $_POST['password']
);

echo json_encode($test_json);

?>
