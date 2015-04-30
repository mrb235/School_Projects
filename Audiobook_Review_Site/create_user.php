<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'storedInfo.php';
session_start();

$mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
if ($mysqli->connect_errno) {
  $output_json = array(
    'success' => 'false',
    'output' => 'failed to connect'
  );
echo "it didn't worked";
}
if(strlen($_POST['username']) < 1) {
  $output_json = array(
    'success' => 'false',
    'output' => 'Please enter a username'
  );
echo json_encode($output_json);
die();
}

if(strlen($_POST['password']) < 1) {
  $output_json = array(
    'success' => 'false',
    'output' => 'Please enter a password'
  );
echo json_encode($output_json);
die();
}

$usr_pwd = array(
  'name' => $_POST['username'],
  'pass' => $_POST['password']
);



if(!($user_pass_obj = $mysqli->prepare('SELECT name, password FROM user'))) {
  $output_json = array(
    'success' => 'false',
    'output' => 'prepare failed'
  );
echo json_encode($output_json);
}

if (!$user_pass_obj->execute()) {
  $output_json = array(
    'success' => 'false',
    'output' => 'execute failed'
  );
echo json_encode($output_json);
}

$users = null;
$passwords = null;
$userExists = false;

if (!$user_pass_obj->bind_result($users, $passwords)) {
  $output_json = array(
    'success' => 'false',
    'output' => 'bind failed'
  );
  echo json_encode($output_json);
}


while($user_pass_obj->fetch()) {
  if ($usr_pwd['name'] == $users) {
    $userExists = true;
  }
}

$user_pass_obj->close();

if ($userExists) {
  $output_json = array(
    'success' => 'false',
    'output' => 'username already exists'
  );
}
else {
  
  if(!($create_user = $mysqli->prepare('INSERT INTO user(name, password) VALUES(?, ?)'))) {
    $output_json = array(
      'success' => 'false',
      'output' => 'prepare create user failed'
    );
    echo json_encode($output_json);
  }
  if(!$create_user->bind_param('ss', $usr_pwd['name'], $usr_pwd['pass'])) {
    $output_json = array(
      'success' => 'false',
      'output' => 'bind for create user failed'
    );
    echo json_encode($output_json);
  }
  if(!$create_user->execute()) {
    $output_json = array(
      'success' => 'false',
      'output' => 'execute for create user failed'
    );
    echo json_encode($output_json);
  }

  $output_json = array(
    'success' => 'true',
    'output' => 'Your now a member!'
  );

  $_SESSION['loggedIn'] = true;
  $_SESSION['user'] = $usr_pwd['name'];
}

echo json_encode($output_json);

?>
