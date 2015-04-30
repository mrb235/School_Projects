<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'storedInfo.php';
session_start();

$mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
if ($mysqli->connect_errno) {
  echo "it didn't worked";
}
if(!($audiobook_stmt = $mysqli->prepare('SELECT * FROM audiobook'))) {
  echo 'prepare failed';
}
if (!$audiobook_stmt->execute()) {
echo "execute failed";
}
$id = null;
$name = null;
$author = null;
$narrator = null;
$date_published = null;
$description = null;
$ISBN = null;
$length_hr = null;
$length_min = null;
$bookJSON = null;
if (!$audiobook_stmt->bind_result($id, $name, $author, $narrator, $date_published,
  $description, $ISBN, $length_hr, $length_min)) {
  echo 'bind failed';
}
while($audiobook_stmt->fetch()) {
  if($_POST['title'] == $name) {
    $bookJSON = array(
      'id' => $id,
      'name' => $name,
      'author' => $author,
      'narrator' => $narrator,
      'date_published' => $date_published,
      'description' => $description,
      'ISBN' => $ISBN,
      'length_hr' => $length_hr,
      'length_min' => $length_min,
    );

    break;
  }
}
$audiobook_stmt->close();

echo json_encode($bookJSON);
?>
