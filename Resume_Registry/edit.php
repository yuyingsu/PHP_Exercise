<?php
session_start();

if (!isset($_SESSION["user_id"]) ) {
  die('ACCESS DENIED');
  return;
}

$pdo = new PDO('mysql:host=localhost;port=3306;dbname=misc', 'fred', 'zap');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  if ( isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary']) && isset($_SESSION["user_id"])) {
        if( strpos( $_POST['email'], '@') === false ) {
          $_SESSION['error']="Email address must contain @";
          header("Location: edit.php?profile_id=".$_POST['profile_id']);
          return;
        }
        $sql = "UPDATE profile SET user_id = :user_id, first_name = :first_name,
                last_name = :last_name, email = :email, headline = :headline,
                summary = :summary
                WHERE profile_id = :profile_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
            ':profile_id' => $_GET['profile_id'],
            ':user_id' => $_SESSION['user_id'],
            ':first_name' => $_POST['first_name'],
            ':last_name' => $_POST['last_name'],
            ':email' => $_POST['email'],
            ':headline' => $_POST['headline'],
            ':summary' => $_POST['summary']));
        $_SESSION['success'] = 'Record updated';
        header( 'Location: index.php' ) ;
        return;
      }
      $stmt = $pdo->prepare("SELECT * FROM profile where profile_id = :xyz");
      $stmt->execute(array(":xyz" => $_GET['profile_id']));
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      $n = htmlentities($row['first_name']);
      $e = htmlentities($row['last_name']);
      $p = htmlentities($row['email']);
      $q = htmlentities($row['headline']);
      $l = htmlentities($row['summary']);
      $profile_id = $row['profile_id'];
?>
<p>Editing Profile for UMSI</p>
<?php
if ( isset($_SESSION['error']) ) {
    echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
    unset($_SESSION['error']);
}
?>
<form method="post">
<p>First Name:
<input type="text" name="first_name" value="<?= $n ?>"></p>
<p>Last Name:
<input type="text" name="last_name" value="<?= $e ?>"></p>
<p>Email:
<input type="text" name="email" value="<?= $p ?>"></p>
<p>Headline:
<input type="text" name="headline" value="<?= $q ?>"></p>
<p>Summary:
<input type="text" name="summary" value="<?= $l ?>"></p>
<input type="hidden" name="profile_id" value="<?= $profile_id ?>">
<p><input type="submit" value="Save"/>
<a href="index.php">Cancel</a>
</form>
