<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

echo '
<!DOCTYPE html>
<html>
   <head>
     <meta charset="utf-8">
     <title>final project testing</title>
     <script src="final.js"></script>
     <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script
     <link rel="stylesheet" type="text/css" href="final.css">
 
   </head>
   <body>
     <a class="not_logged_in" href="create_user.html">Create user</a> <br/>
     <form class="not_logged_in" id="userForm">
       <input type="text" id="enter_username" name="enter_username">
       <div class="notification" id="username_error"></div>
       <br>
       <input type="password" id="enter_password" name="enter_username">
       <div class="notification" id="password_error"></div>
       <br>
       <input type="button" value="test" id="username_form">
     </form>
     <form class="logged_in hide" id="user_options">
       <input type="button" value="Account Details" id="Account Details">
       <input type="button" value="Audiobooks" id="Audiobooks">
       <input type="button" value="Log Out" id="logout">
     </form>
     <br>
     <div class="notification" id="output_div"></div>
     <div id="main_audiobook"></div>
   </body>
 </html>
';

?>
