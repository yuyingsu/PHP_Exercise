<?php
session_start();

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
    <h1>Welcome to the Automobiles Database</h1>
    <?php
      if ( isset($_SESSION['success']) ) {
        echo('<p style="color: green;">'.htmlentities($_SESSION['success'])."</p>\n");
        unset($_SESSION['success']);
      }
      ?>
      <div>
        <?php
          $pdo = new PDO('mysql:host=localhost;port=3306;dbname=misc', 'fred', 'zap');
          $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
          if(isset($_SESSION['name'])){
          $stmt = $pdo->query('SELECT * from autos');
          if(!$row = $stmt->fetch(PDO::FETCH_ASSOC))
          {
            echo('No rows found.');
          }
          else {
            echo('<table border="1">'."\n");
            echo "<tr>";
            echo "<th>Make</th>";
            echo "<th>Model</th>";
            echo "<th>Mileage</th>";
            echo "<th>Year</th>";
            echo "<th>Action</th>";
            echo "</tr>";
            echo "<tr><td>";
            echo(htmlentities($row['make']));
            echo("</td><td>");
            echo(htmlentities($row['model']));
            echo("</td><td>");
            echo(htmlentities($row['mileage']));
            echo("</td><td>");
            echo(htmlentities($row['year']));
            echo("</td><td>");
            echo('<a href="edit.php?autos_id='.$row['autos_id'].'">Edit</a> / ');
            echo('<a href="delete.php?autos_id='.$row['autos_id'].'">Delete</a>');
            echo("</td></tr>\n");
          }
          while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
              echo "<tr><td>";
              echo(htmlentities($row['make']));
              echo("</td><td>");
              echo(htmlentities($row['model']));
              echo("</td><td>");
              echo(htmlentities($row['mileage']));
              echo("</td><td>");
              echo(htmlentities($row['year']));
              echo("</td><td>");
              echo('<a href="edit.php?autos_id='.$row['autos_id'].'">Edit</a> / ');
              echo('<a href="delete.php?autos_id='.$row['autos_id'].'">Delete</a>');
              echo("</td></tr>\n");
          }
        }
        ?>
        </table>
      </div>
      <form method="post">
        <br>
        <?php
        if(!isset($_SESSION['name']))
        {
          echo('<a href="login.php">Please log in</a>');
        }
        ?>
        <br>
        <?php
        if(isset($_SESSION['name']))
        {
          echo('<a href="add.php">Add New Entry</a>');
          echo('<br>');
          echo('<a href="logout.php">Log out</a>');
        }
        ?>
      </form>
    </div>
  </body>
  </html>
