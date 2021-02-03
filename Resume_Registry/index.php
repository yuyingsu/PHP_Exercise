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
  <title>Yuying Su's Resume Registry</title>
  <?php require_once "bootstrap.php"; ?>
</head>
<body>
  <div class="container">
    <h1>Yuying Su's Resume Registry</h1>
    <?php
      if ( isset($_SESSION['success']) ) {
        echo('<p style="color: green;">'.htmlentities($_SESSION['success'])."</p>\n");
        unset($_SESSION['success']);
      }
      ?>
      <div>
        <?php
          $space=' ';
          $pdo = new PDO('mysql:host=localhost;port=3306;dbname=misc', 'fred', 'zap');
          $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
          $stmt = $pdo->query('SELECT * from profile');
          if(!$row = $stmt->fetch(PDO::FETCH_ASSOC))
          {
            echo('No rows found.');
          }
          else {
            echo('<table border="1">'."\n");
            echo "<tr>";
            echo "<th>Name</th>";
            echo "<th>Headline</th>";
            if(isset($_SESSION['user_id']))
            {
              echo "<th>Action</th>";
            }
            echo "</tr>";
            echo "<tr><td>";
            echo('<a href="view.php?profile_id='.$row['profile_id'].'">'.$row['first_name'].''.$space.' '.$row['last_name'].'</a>');
            echo("</td><td>");
            echo(htmlentities($row['headline']));
            if(isset($_SESSION['user_id']))
            {
            echo("</td><td>");
            echo('<a href="edit.php?profile_id='.$row['profile_id'].'">Edit</a> ');
            echo('<a href="delete.php?profile_id='.$row['profile_id'].'">Delete</a>');
            }
            echo("</td></tr>\n");
          }
          while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr><td>";
            echo('<a href="view.php?profile_id='.$row['profile_id'].'">'.$row['first_name'].''.$space.''.$row['last_name'].'</a>');
            echo("</td><td>");
            echo(htmlentities($row['headline']));
            echo("</td><td>");
            if(isset($_SESSION['user_id']))
            {
              echo("</td><td>");
              echo('<a href="edit.php?profile_id='.$row['profile_id'].'">Edit</a> ');
              echo('<a href="delete.php?profile_id='.$row['profile_id'].'">Delete</a>');
            }
            echo("</td></tr>\n");
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
