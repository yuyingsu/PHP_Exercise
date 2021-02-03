<?php
session_start();
$pdo = new PDO('mysql:host=localhost;port=3306;dbname=misc', 'fred', 'zap');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
unset($_SESSION['name']);
unset($_SESSION['user_id']);

if ( isset($_POST['cancel'] ) ) {
    header("Location: index.php");
    return;
}

$salt = 'XyZzy12*_';

// Check to see if we have some POST data, if we do process it
if ( isset($_POST['name']) && isset($_POST['pass']) ) {
    if ( strlen($_POST['name']) < 1 || strlen($_POST['pass']) < 1 ) {
          $_SESSION['error'] = "User name and password are required";
          header("Location: login.php");
          return;
    }
    $check = hash('md5', $salt.$_POST['pass']);
    $stmt = $pdo->prepare('SELECT user_id, name FROM users
            WHERE email = :em AND password = :pw');  
    $stmt -> execute(array(
                  ':em'=> $_POST['name'],
                  ':pw'=> $check));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row !== false) {
      $_SESSION['name']= $row['name'];
      $_SESSION['user_id']= $row['user_id'];
      header("Location: index.php");
      return;
    } else {
      $_SESSION['error']= "Incorrect password";
      header("Location: login.php");
      return;
    }
  }

// Fall through into the View
?>
<!DOCTYPE html>
<html>
<head>
<?php require_once "bootstrap.php"; ?>
<script>
function doValidate(){
    console.log('Validating...');
    try {
    address = document.getElementById('nam').value;
    pw = document.getElementById('id_1723').value;
    console.log("Validating address="+address);
    console.log("Validating pw="+pw);
    if (pw == null || pw == "" || address == null || address == "") {
      alert("Both fields must be filled out");
      return false;
    }
    if(address.indexOf("@")==-1)
    {
      alert("Email address must contain @");
      return false;
    }
    return true;
    } catch(e) {
    return false;
    }
    return false;
}
</script>
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
<input type="submit" onclick="return doValidate();" value="Log In">
<input type="submit" name="cancel" value="Cancel">
</form>
</div>
</body>
