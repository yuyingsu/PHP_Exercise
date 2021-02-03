<?php
session_start();
// Demand a GET parameter
if (!isset($_SESSION["name"]) ) {
  die('Not logged in');
}

// If the user requested logout go back to index.php
if ( isset($_POST['logout']) ) {
  header('Location: logout.php');
  return;
}
if ( isset($_POST['addnew']) ) {
  header('Location: add.php');
  return;
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Yuying Su's Auto Tracker</title>
  <?php require_once "bootstrap.php"; ?>
</head>
<body>
  <div class="container">
    <h1>Tracking Autos for <?php if ( isset($_SESSION["name"]) ) {
      echo htmlentities($_SESSION['name']);
    } ?></h1>
    <?php
      if ( isset($_SESSION['success']) ) {
        echo('<p style="color: green;">'.htmlentities($_SESSION['success'])."</p>\n");
        unset($_SESSION['success']);
      }
      ?>
      <div>
        <form method="post">
          <br>
          <input type="submit" name="addnew" value="Add">
          <input type="submit" name="logout" value="Log out"><br>
        </form>
        <?php
          $pdo = new PDO('mysql:host=localhost;port=3306;dbname=misc', 'fred', 'zap');
          $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
          $stmt = $pdo->query('SELECT * from autos');
          echo "<br>";
          echo "<ul>";
          while( $row=$stmt->fetch(PDO::FETCH_ASSOC))
          {
            echo "<li>";
            echo($row['make']);
            echo "&nbsp";
            echo($row['year']);
            echo "&nbsp";
            echo($row['mileage']);
            echo "</li>";
          }
          echo "<br>";
          echo "<ul>";
        ?>
      </div>
    </div>
  </body>
  </html>
