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

if ( isset($_POST['make']) && isset($_POST['mileage']) && isset($_POST['year']) ) {
      if( !is_numeric($_POST['mileage']) ) {
        $_SESSION['error']="mileage should be number";
        header("Location: add.php");
        return;
      }else if( !is_numeric($_POST['year']) ) {
        $_SESSION['error']="year should be number";
        header("Location: add.php");
        return;
      }
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
    <h1>Tracking Autos for <?php if ( isset($_SESSION['name']) ) {
      echo htmlentities($_SESSION['name']);
    } ?></h1>
    <?php
    if ( isset($_SESSION['error']) ) {
      echo('<p style="color: red;">'.htmlentities($_SESSION['error'])."</p>\n");
      unset($_SESSION['error']);
      }
    if ( isset($_POST['add'])) {
           $pdo = new PDO('mysql:host=localhost;port=3306;dbname=misc', 'fred', 'zap');
           $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
           $stmt = $pdo->prepare('INSERT INTO autos
             (make, year, mileage) VALUES ( :mk, :yr, :mi)');
             $stmt->execute(array(
               ':mk' => $_POST['make'],
               ':yr' => $_POST['year'],
               ':mi' => $_POST['mileage'])
             );
             echo '<span style="color:green">Record inserted.</span>';
             $_SESSION['success'] = "Record inserted";
             header("Location: view.php");
             return;
           }
      ?>
      <form method="post">
        <br>
        <label for="make">Make:</label><br>
        <input type="text" id="make" name="make"><br><br>
        <label for="mileage">Mileage:</label><br>
        <input type="text" id="mileage" name="mileage"><br><br>
        <label for="year">Year:</label><br>
        <input type="text" id="year" name="year"><br><br>
        <input type="submit" name="add" value="Add">
        <input type="submit" name="logout" value="Log out"><br>
      </form>
    </div>
  </body>
  </html>
