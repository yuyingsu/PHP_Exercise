<?php
session_start();

$pdo = new PDO('mysql:host=localhost;port=3306;dbname=misc', 'fred', 'zap');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$stmt = $pdo->prepare("SELECT * FROM profile where profile_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$n = htmlentities($row['first_name']);
$e = htmlentities($row['last_name']);
$p = htmlentities($row['email']);
$q = htmlentities($row['headline']);
$r = htmlentities($row['summary']);
$profile_id = $row['profile_id'];

?>
<p>Profile information</p>
<?php
if ( isset($_SESSION['error']) ) {
    echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
    unset($_SESSION['error']);
}
?>
<form method="post">
<p>First Name:<?= $n ?></p>
<p>Last Name:<?= $e ?></p>
<p>Email:<?= $p ?></p>
<p>Headline:</p>
<p><?= $q ?></p>
<p>Summary:</p>
<p><?= $r ?></p>
<input type="hidden" name="profile_id" value="<?= $profile_id ?>">
<a href="index.php">Done</a>
</form>
