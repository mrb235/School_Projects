<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'storedInfo.php';
session_start();

if(isset($_SESSION['loggedIn'])) {
  if($_SESSION['loggedIn']) {
    echo $_SESSION['user'];
  }else {
    echo 'false';
  }
}
else {
  echo 'false';
}
?>
