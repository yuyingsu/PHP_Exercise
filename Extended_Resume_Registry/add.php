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
      $profile_id = $pdo->lastInsertId();
      $ran = 1;
      for($i=1;$i<=9;$i++)
      {
        if(!isset($_POST['year'.$i])) continue;
        if(!isset($_POST['desc'.$i])) continue;
        $year = $_POST['year'.$i];
        $desc = $_POST['desc'.$i];
        if ( strlen($year) == 0 || strlen($desc) == 0 ) {
          $_SESSION['error'] ="All fields are required";
          header("Location: add.php");
        }

        if ( ! is_numeric($year) ) {
          $_SESSION['error'] = "Position year must be numeric";
          header("Location: add.php");
        }
        $stmt = $pdo->prepare('INSERT INTO position (profile_id, rank, year, description)
                      VALUES (:pid, :ran, :year, :desc)');
        $stmt->execute(array(
            ':pid' => $profile_id,
            ':ran' => $ran,
            ':year' => $year,
            ':desc' => $desc)
        );
        $ran++;
      }

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
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">
  <script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>
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
        <textarea name="summary" rows="8" cols="80"></textarea><br><br>
        <p>
          Position:<input type="submit" id="addPos" value="+">
          <div id="position_fields">
          </div>
        </p>
        <input type="submit" name="add" value="Add">
        <input type="submit" name="cancel" value="Cancel"><br>
      </form>
      <script>
      countPos=0;
      $(document).ready(function()
      {
          console.log('Document ready called');
          $('#addPos').click(function(event){
            event.preventDefault();
            if (countPos>=9) {
              alert("Maxium of nine position entires exceeded");
              return;
            }
            countPos++;
            var div = document.createElement('div');
            div.id="position"+countPos;
            $('#position_fields').append(div);
            var input2 = document.createElement("input");
            input2.type="button";
            input2.value="Delete";
            input2.onclick=function(){$('#'+div.id).remove(); return false; };
            div.append(input2);
            var br = document.createElement("br");
            div.append(br);
            var br = document.createElement("br");
            div.append(br);
            var p = document.createElement("p");
            p.innerHTML="Year: ";
            div.append(p);
            var input1 = document.createElement("input");
            input1.type='text';
            input1.name="year"+countPos;
            p.append(input1);
            var area = document.createElement("textarea");
            area.name="desc"+countPos;
            area.rows=8;
            area.cols=80;
            div.append(area);
            console.log("Adding position "+countPos);
            });
        });
      </script>
    </div>
  </body>
  </html>
