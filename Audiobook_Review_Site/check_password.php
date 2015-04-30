<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'storedInfo.php';
session_start();

$mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
if ($mysqli->connect_errno) {
  echo "it didn't worked";
}
if(!($user_pass_obj = $mysqli->prepare('SELECT name, password FROM user'))) {
  echo 'prepare failed';
}

if (!$user_pass_obj->execute()) {
echo "execute failed";
}

$users = null;
$passwords = null;
$correctpwd = false;
$strangepw = 'nope';


if (!$user_pass_obj->bind_result($users, $passwords)) {
  echo 'bind failed';
}

while($user_pass_obj->fetch()) {
  if ($_POST['username'] == $users) {
    if ($_POST['password'] == $passwords) {
      $correctpwd = true;
      $_SESSION['loggedIn'] = 'true';
      break;
    }
  }
}

if($correctpwd) {
  echo 'true';
}
else {
  echo 'false';
}

$user_pass_obj->close();
?>
