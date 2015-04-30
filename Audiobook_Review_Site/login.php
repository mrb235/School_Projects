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
    if ($usr_pwd['pass'] == $passwords) {
      $logInOk = true;
    } else {
      $logInOk = false;
    }
  }
}
if ($userExists && $logInOk) {
  $output_json = array(
    'success' => 'true',
    'output' => 'logged in Successfully'
  );
  $_SESSION['user'] = $usr_pwd['name'];
  $_SESSION['loggedIn'] = true;
}
else if ($userExists) {
  $output_json = array(
    'success' => 'true',
    'output' => 'user exists but wrong password'
  );
}
else {
  $output_json = array(
    'success' => 'false',
    'output' => 'This username doesn\'t exist'
  );
}
  

echo json_encode($output_json);

?>
