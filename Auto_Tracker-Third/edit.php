<?php
session_start();
$pdo = new PDO('mysql:host=localhost;port=3306;dbname=misc', 'fred', 'zap');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  if ( isset($_POST['make']) && isset($_POST['mileage']) && isset($_POST['year']) && isset($_POST['model']) && isset($_POST['autos_id']) ) {
        if( !is_numeric($_POST['mileage']) ) {
          $_SESSION['error']="mileage should be number";
          header("Location: edit.php?autos_id=".$_POST['autos_id']);
          return;
        }else if( !is_numeric($_POST['year']) ) {
          $_SESSION['error']="year should be number";
          header("Location: edit.php?autos_id=".$_POST['autos_id']);
          return;
        }
        $sql = "UPDATE autos SET make = :make,
                model = :model, year = :year,
                mileage = :mileage
                WHERE autos_id = :autos_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
            ':make' => $_POST['make'],
            ':model' => $_POST['model'],
            ':year' => $_POST['year'],
            ':mileage' => $_POST['mileage'],
            ':autos_id' => $_POST['autos_id']));
        $_SESSION['success'] = 'Record updated';
        header( 'Location: index.php' ) ;
        return;
      }
      $stmt = $pdo->prepare("SELECT * FROM autos where autos_id = :xyz");
      $stmt->execute(array(":xyz" => $_GET['autos_id']));
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      $n = htmlentities($row['make']);
      $e = htmlentities($row['model']);
      $p = htmlentities($row['year']);
      $q = htmlentities($row['mileage']);
      $autos_id = $row['autos_id'];
?>
<p>Edit User</p>
<?php
if ( isset($_SESSION['error']) ) {
    echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
    unset($_SESSION['error']);
}
?>
<form method="post">
<p>Make:
<input type="text" name="make" value="<?= $n ?>"></p>
<p>Model:
<input type="text" name="model" value="<?= $e ?>"></p>
<p>Year:
<input type="text" name="year" value="<?= $p ?>"></p>
<p>Mileage:
<input type="text" name="mileage" value="<?= $q ?>"></p>
<input type="hidden" name="autos_id" value="<?= $autos_id ?>">
<p><input type="submit" value="Save"/>
<a href="index.php">Cancel</a>
</form>
