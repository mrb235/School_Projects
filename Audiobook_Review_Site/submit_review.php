<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'storedInfo.php';
session_start();

$review = htmlspecialchars($_POST['review']);

$mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
if ($mysqli->connect_errno) {
  echo "it didn't worked";
}
if(!($audiobook_stmt = $mysqli->prepare('SELECT user.id, user.name FROM user'))) {
  echo 'prepare failed';
}
if (!$audiobook_stmt->execute()) {
echo "execute failed";
}
$user_id = null;
$user_name = null;
$user_result = null;
if (!$audiobook_stmt->bind_result($user_id, $user_name)) {
  echo 'bind failed';
}
while($audiobook_stmt->fetch()) {
  if($_POST['user'] == $user_name) {
    $user_result = $user_id;
    break;
  }
}
$audiobook_stmt->close();

if(!($audiobook_stmt = $mysqli->prepare('INSERT INTO review_audiobook(
  review_audiobook.audiobook_id, 
  review_audiobook.user_id, 
  review_audiobook.review) 
  VALUES(?, ?, ?)'))) {
  echo 'prepare review input failed';
}
if(!$audiobook_stmt->bind_param('iis',$_POST['audiobook_id'], $user_result, $review)) {
  echo 'bind params for review input failed';
}
if (!$audiobook_stmt->execute()) {
echo "execute input review failed";
}


?>
