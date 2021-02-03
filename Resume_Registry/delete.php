<?php
session_start();
$pdo = new PDO('mysql:host=localhost;port=3306;dbname=misc', 'fred', 'zap');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (isset($_POST['delete']) && isset($_POST['profile_id'])) {
    $sql = "DELETE FROM profile WHERE profile_id = :zip";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(':zip' => $_GET['profile_id']));
    $_SESSION['success'] = 'Record deleted';
    header( 'Location: index.php' ) ;
    return;
}
$stmt = $pdo->prepare("SELECT * FROM profile where profile_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$n = htmlentities($row['first_name']);
$m = htmlentities($row['last_name']);
$autos_id = $row['profile_id'];
?>
<h1>Deleting Profile</h1>
<p>First Name: <?= $n ?></p>
<p>Last Name: <?= $m ?></p>

<form method="post">
<input type="hidden" name="profile_id" value="<?= $profile_id ?>">
<input type="submit" value="Delete" name="delete">
<a href="index.php">Cancel</a>
</form>
