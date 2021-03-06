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

$stmt = $pdo->prepare("SELECT * FROM position where profile_id=:prof order by rank");
$stmt->execute(array(':prof'=>$profile_id));
$position = array();
while($row = $stmt->fetch(PDO::FETCH_ASSOC))
{
          $position[]=$row;
}

$stmt = $pdo->prepare("SELECT year,name FROM Education join Institution on
      Education.institution_id=Institution.institution_id where
      profile_id=:prof order by rank");
$stmt->execute(array(':prof'=>$profile_id));
$educations = array();
while($row = $stmt->fetch(PDO::FETCH_ASSOC))
{
          $educations []=$row;
}

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
<p>Education:</p>
<ul id="pos1">
</ul>
<p>Position:</p>
<ul id="pos">
</ul>
<input type="hidden" name="profile_id" value="<?= $profile_id ?>">
<a href="index.php">Done</a>
</form>
<script>
  var passedArray = <?php echo json_encode($position); ?>;
  var passedArray1 = <?php echo json_encode($educations); ?>;
  for(var i=0;i<passedArray.length;i++)
  {
    var li=document.createElement('LI');
    var textnode = document.createTextNode(passedArray[i]['year']+":"+passedArray[i]['description']);
    li.appendChild(textnode);
    document.getElementById("pos").appendChild(li);
  }
  for(var i=0;i<passedArray1.length;i++)
  {
    var li=document.createElement('LI');
    var textnode = document.createTextNode(passedArray1[i]['year']+":"+passedArray1[i]['name']);
    li.appendChild(textnode);
    document.getElementById("pos1").appendChild(li);
  }
</script>
