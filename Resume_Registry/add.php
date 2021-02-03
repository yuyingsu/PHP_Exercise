<?php
session_start();
// Demand a GET parameter
if (!isset($_SESSION["user_id"]) ) {
  die('ACCESS DENIED');
  return;
}

// If the user requested logout go back to index.php
if ( isset($_POST['logout']) ) {
  header('Location: logout.php');
  return;
}

if ( isset($_POST['cancel']) ) {
  header('Location: index.php');
  return;
}

if ( isset($_POST['fname']) && isset($_POST['lname']) && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary'])) {
      if(strlen($_POST['fname']) < 1 || strlen($_POST['lname']) < 1 || strlen($_POST['email']) < 1 ||
      strlen($_POST['headline']) < 1 || strlen($_POST['summary']) < 1)
      {
        $_SESSION['error'] = "All fields are required";
        header("Location: add.php");
      }
      if( strpos( $_POST['email'], '@') === false ) {
        $_SESSION['error']="Email address must contain @";
        header("Location: add.php");
        return;
      }
      $pdo = new PDO('mysql:host=localhost;port=3306;dbname=misc', 'fred', 'zap');
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $sql = 'INSERT INTO profile (user_id, first_name, last_name, email, headline, summary)
                VALUES (:uid, :fn, :lna, :em, :he, :su)';
      $stmt = $pdo->prepare($sql);
      $stmt->execute(array(
          ':uid' => $_SESSION['user_id'],
          ':fn' => $_POST['fname'],
          ':lna' => $_POST['lname'],
          ':em' => $_POST['email'],
          ':he' => $_POST['headline'],
          ':su' => $_POST['summary']));
      $_SESSION['success'] = 'Profile added';
      header( 'Location: index.php' ) ;
      return;
    }
// Flash pattern

?>
<!DOCTYPE html>
<html>
<head>
  <title>Yuying Su's Auto Tracker</title>
  <?php require_once "bootstrap.php"; ?>
</head>
<body>
  <div class="container">
    <h1>Adding Profile for UMSI</h1>
    <?php
    if ( isset($_SESSION['error']) ) {
        echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
        unset($_SESSION['error']);
    }
      ?>
      <form method="post">
        <br>
        <label for="fname">First Name:</label><br>
        <input type="text" id="fname" name="fname"><br><br>
        <label for="lname">Last Name:</label><br>
        <input type="text" id="lname" name="lname"><br><br>
        <label for="email">Email:</label><br>
        <input type="text" id="email" name="email"><br><br>
        <label for="headline">Headline:</label><br>
        <input type="text" id="headline" name="headline"><br><br>
        <label for="summary">Summary:</label><br>
        <input type="text" id="summary" name="summary"><br><br>
        <input type="submit" name="add" value="Add">
        <input type="submit" name="cancel" value="Cancel"><br>
      </form>
    </div>
  </body>
  </html>
