<?php
session_start();
if ( isset($_POST['cancel'] ) ) {
    header("Location: index.php");
    return;
}

$salt = 'XyZzy12*_';

$stored_hash = '1a52e17fa899cf40fb04cfc42e6352f1';  // Pw is php123

$pattern = "/@/";

// Check to see if we have some POST data, if we do process it
if ( isset($_POST['name']) && isset($_POST['pass']) ) {
    unset($_SESSION["name"]);
    if ( strlen($_POST['name']) < 1 || strlen($_POST['pass']) < 1 ) {
          $_SESSION['error'] = "User name and password are required";
    } else {
        if( !preg_match($pattern, $_POST['name']) ) {
          $_SESSION['error'] = "Email must have an at-sign (@)";
          header("Location: login.php");
          error_log("Login fail ".$_POST['name']." $check");
          return;
        }else {
          $check = hash('md5', $salt.$_POST['pass']);
          if ( $check == $stored_hash ) {
              // Redirect the browser to index.php
              error_log("Login success ".$_POST['name']);
              $_SESSION["name"] = $_POST["name"];
              $_SESSION["success"] = "Logged in.";
              header("Location: index.php");
              return;
          } else {
              $_SESSION['error'] = "Incorrect password";
              header("Location: login.php");
              error_log("Login fail ".$_POST['name']." $check");
              return;
          }
        }
    }
  }

// Fall through into the View
?>
<!DOCTYPE html>
<html>
<head>
<?php require_once "bootstrap.php"; ?>
<title>Yuying Su's Login Page</title>
</head>
<body>
<div class="container">
<h1>Please Log In</h1>
<?php

  if ( isset($_SESSION['error']) ) {
    echo('<p style="color: red;">'.htmlentities($_SESSION['error'])."</p>\n");
    unset($_SESSION['error']);
}
?>
<form method="POST">
<label for="name">Email</label>
<input type="text" name="name" id="nam"><br/>
<label for="id_1723">Password</label>
<input type="text" name="pass" id="id_1723"><br/>
<input type="submit" value="Log In">
<input type="submit" name="cancel" value="Cancel">
</form>
</div>
</body>
