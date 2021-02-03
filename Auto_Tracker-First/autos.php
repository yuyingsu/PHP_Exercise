<?php

// Demand a GET parameter
if ( ! isset($_GET['name']) || strlen($_GET['name']) < 1  ) {
  die('Name parameter missing');
}

// If the user requested logout go back to index.php
if ( isset($_POST['logout']) ) {
  header('Location: index.php');
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
    <h1>Tracking Autos for <?php if ( isset($_REQUEST['name']) ) {
      echo htmlentities($_REQUEST['name']);
    } ?></h1>
    <?php
    if ( isset($_REQUEST['name']) ) {
      echo "<p>Welcome: ";
      echo htmlentities($_REQUEST['name']);
      echo "</p>\n";
    }

    if ( !isset($_REQUEST['make']) ) {
      echo "Make is required ";
    } else if ( isset($_REQUEST['mileage']) && isset($_REQUEST['year']) ) {
      if( !is_numeric($_REQUEST['mileage']) ) {
        echo "Mileage should be number. ";
      }else if( !is_numeric($_REQUEST['year']) ) {
        echo "Year should be number ";
      }
      if ( isset($_POST['add']) ) {
        global $pdo;
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
        }
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

      <div>
        <?php
        if ( isset($_POST['add']) ) {
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
        }
        ?>
      </div>
    </div>
  </body>
  </html>
