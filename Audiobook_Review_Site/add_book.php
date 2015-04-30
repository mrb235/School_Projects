<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'storedInfo.php';
session_start();

$mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
if ($mysqli->connect_errno) {
  echo "it didn't worked";
}
if(!($audiobook_stmt = $mysqli->prepare('
        INSERT INTO audiobook(
            audiobook.name, 
            audiobook.author, 
            audiobook.narrator, 
            audiobook.length_hr, 
            audiobook.length_min,
            audiobook.date_published, 
            audiobook.description, 
            audiobook.ISBN) 
        VALUES(?, ?, ?, ?, ?, ?, ?, ?)'))) {
  echo 'prepare failed';
      }
if (!$audiobook_stmt->bind_param(
    'sssiisss',
        $_POST['name'], 
        $_POST['author'], 
        $_POST['narrator'], 
        $_POST['length_hr'],
        $_POST['length_min'], 
        $_POST['date_published'], 
        $_POST['description'], 
        $_POST['ISBN'])){
    echo 'bind param failed';
  }

if (!$audiobook_stmt->execute()) {
echo "execute failed";
}
$audiobook_stmt->close();
?>
