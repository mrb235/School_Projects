<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'storedInfo.php';
session_start();

$mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
if ($mysqli->connect_errno) {
  echo "it didn't worked";
}
if(!($audiobook_stmt = $mysqli->prepare('SELECT audiobook.name FROM audiobook ORDER BY audiobook.name'))) {
  echo 'prepare failed';
      }
if (!$audiobook_stmt->execute()) {
echo "execute failed";
}
$nameArray = array();
$name = null;
if (!$audiobook_stmt->bind_result($name)) {
  echo 'bind failed';
}
while($audiobook_stmt->fetch()) {
  $nameArray[] = $name;
}

echo json_encode($nameArray);

$audiobook_stmt->close();
?>
