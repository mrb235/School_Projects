<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'storedInfo.php';
session_start();

$mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
if ($mysqli->connect_errno) {
  echo "it didn't worked";
}

if(!($audiobook_stmt = $mysqli->prepare('SELECT user.name, review_audiobook.review 
  FROM review_audiobook
  INNER JOIN user ON user.id = review_audiobook.user_id
  INNER JOIN audiobook ON review_audiobook.audiobook_id = audiobook.id
  WHERE audiobook.id = ?
  ORDER BY review_audiobook.id DESC
  '))) {
  echo 'prepare failed';
  }
if (!$audiobook_stmt->bind_param('i', $_POST['title_id'])) {
  echo 'bind param for book review get failed';
}
if (!$audiobook_stmt->execute()) {
echo "execute failed";
}
$name = null;
$review = null;
$reviewJSON = array();
if (!$audiobook_stmt->bind_result($name, $review)) {
  echo 'bind failed';
}

while($audiobook_stmt->fetch()) {
  $individual_review = array(
    'user_name' => $name,
    'review' => $review
  );
  $reviewJSON[] = $individual_review;
}
$audiobook_stmt->close();

echo json_encode($reviewJSON);
?>
