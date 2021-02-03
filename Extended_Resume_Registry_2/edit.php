<?php
session_start();

if (!isset($_SESSION["user_id"]) ) {
  die('ACCESS DENIED');
  return;
}
if(!isset($_REQUEST["profile_id"])){
  $_SESSION['error']="Missing profile_id";
  header('Location: index.php');
  return;
}
$pdo = new PDO('mysql:host=localhost;port=3306;dbname=misc', 'fred', 'zap');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$stmt = $pdo->prepare('SELECT * FROM Profile WHERE profile_id=:pid and user_id=:uid');
$stmt->execute(array( ':pid' => $_REQUEST['profile_id'], ':uid' => $_SESSION['user_id']));
$profile = $stmt->fetch(PDO::FETCH_ASSOC);
if ($profile === false)
{
  $_SESSION['error']="Could not load profile";
  header('Location: index.php');
  return;
}
if ( isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary']) && isset($_SESSION["user_id"])) {
        if( strlen($_POST['first_name'])==0 || strlen($_POST['last_name'])==0 || strlen($_POST['email'])==0
      || strlen($_POST['headline'])==0 || strlen($_POST['summary'])==0) {
          $_SESSION['error']="All fields are required";
          header("Location: edit.php?profile_id=".$_REQUEST['profile_id']);
          return;
        }
        if( strpos( $_POST['email'], '@') === false ) {
          $_SESSION['error']="Email address must contain @";
          header("Location: edit.php?profile_id=".$_REQUEST['profile_id']);
          return;
        }
        $sql = "UPDATE profile SET first_name = :first_name,
                last_name = :last_name, email = :email, headline = :headline,
                summary = :summary
                WHERE profile_id = :profile_id and user_id=:user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
            ':profile_id' => $_REQUEST['profile_id'],
            ':user_id' => $_SESSION['user_id'],
            ':first_name' => $_POST['first_name'],
            ':last_name' => $_POST['last_name'],
            ':email' => $_POST['email'],
            ':headline' => $_POST['headline'],
            ':summary' => $_POST['summary']));

        $stmt = $pdo->prepare('DELETE FROM Position WHERE profile_id=:pid');
        $stmt->execute(array( ':pid' => $_REQUEST['profile_id']));

        $rank = 1;
        for($i=1; $i<=9; $i++) {
          if ( ! isset($_POST['year'.$i]) ) continue;
          if ( ! isset($_POST['desc'.$i]) ) continue;

          $year = $_POST['year'.$i];
          $desc = $_POST['desc'.$i];

          if ( strlen($year) == 0 || strlen($desc) == 0 ) {
            $_SESSION['error'] ="All fields are required";
            header("Location: edit.php?profile_id=".$_REQUEST['profile_id']);
          }

          if ( ! is_numeric($year) ) {
            $_SESSION['error'] = "Position year must be numeric";
            header("Location: edit.php?profile_id=".$_REQUEST['profile_id']);
          }

          $stmt = $pdo->prepare('INSERT INTO Position
            (profile_id, rank, year, description)
            VALUES ( :pid, :rank, :year, :desc)');

            $stmt->execute(array(
              ':pid' => $_REQUEST['profile_id'],
              ':rank' => $rank,
              ':year' => $year,
              ':desc' => $desc)
        );

          $rank++;
      }

      $stmt = $pdo->prepare('DELETE FROM Education WHERE profile_id=:pid');
      $stmt->execute(array( ':pid' => $_REQUEST['profile_id']));

      $rank = 1;
      for($i=1; $i<=9; $i++) {
        if ( ! isset($_POST['edu_year'.$i]) ) continue;
        if ( ! isset($_POST['edu_school'.$i]) ) continue;

        $year = $_POST['edu_year'.$i];
        $school = $_POST['edu_school'.$i];

        if(strlen($year)==0 || strlen($school)==0)
        {
          $_SESSION['error'] ="All fields are required";
          header("Location: edit.php?profile_id=".$_REQUEST['profile_id']);
        }
        if(! is_numeric($year))
        {
          $_SESSION['error'] ="Education year must be numeric";
          header("Location: edit.php?profile_id=".$_REQUEST['profile_id']);
        }

        $institution_id = false;
        $stmt = $pdo->prepare('SELECT institution_id from Institution where name = :name');
        $stmt-> execute(array(':name'=>$school));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row!==false) {
          $institution_id = $row['institution_id'];
        }
        if($institution_id === false)
        {
          $stmt = $pdo->prepare('INSERT INTO Institution
            (name)
            VALUES (:name)');

          $stmt->execute(array(
                        ':name' => $school)
                  );
          $instituion_id = $pdo->lastInsertId();
        }
        $stmt = $pdo->prepare('INSERT INTO Education
            (profile_id, institution_id, rank, year)
            VALUES (:pid, :ins, :rank, :year)');

        $stmt->execute(array(
                    ':pid' => $_REQUEST['profile_id'],
                    ':ins' => $institution_id,
                    ':rank' => $rank,
                    ':year' => $year
                  )
              );
                $rank++;
            }

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
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">

  <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css">

  <script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>

  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js" integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30=" crossorigin="anonymous"></script>

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
<p>
  Education:<input type="submit" id="addEdu" value="+">
  <div id="education_fields">
  </div>
</p>
<p>
  Position:<input type="submit" id="addPos" value="+">
  <div id="position_fields">
  </div>
</p>
<p><input type="submit" value="Save"/>
<a href="index.php">Cancel</a>
</form>
<script>
var record=0;
var passedArray = <?php echo json_encode($position); ?>;
for(var i=0;i<passedArray.length;i++)
{
  var div = document.createElement('div');
  div.id="position"+(i+1);
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
  input1.name="year"+(i+1);
  input1.value=passedArray[i]['year'];
  p.append(input1);
  var area = document.createElement("textarea");
  area.name="desc"+(i+1);
  area.rows=8;
  area.cols=80;
  area.value=passedArray[i]['description'];
  div.append(area);
  record++;
}
var record1=0;
var passedArray1 = <?php echo json_encode($educations); ?>;
for(var i=0;i<passedArray1.length;i++)
{
  var div = document.createElement('div');
  div.id="education"+(i+1);
  $('#education_fields').append(div);
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
  input1.type="text";
  input1.name="edu_year"+(i+1);
  input1.value=passedArray1[i]['year'];
  p.append(input1);
  var p1 = document.createElement("p");
  p1.innerHTML="School: ";
  div.append(p1);
  var input2 = document.createElement("input");
  input2.type="text";
  input2.name="edu_school"+(i+1);
  input2.className="school";
  input2.value=passedArray1[i]['name'];
  p1.append(input2);
  $('.school').autocomplete({ source: "school.php" });
  record1++;
}
$(document).ready(function()
{
    console.log('Document ready called');
    $('#addPos').click(function(event){
      countPos=record;
      record++;
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
      $('#addEdu').click(function(event){
        countEdu=record1;
        record1++;
        event.preventDefault();
        if (countEdu>=9) {
            alert("Maxium of nine position entires exceeded");
            return;
          }
        countEdu++;
        $('#education_fields').append(div);
          var div = document.createElement('div');
          div.id="education"+countEdu;
          $('#education_fields').append(div);
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
          input1.type="text";
          input1.name="edu_year"+(countEdu+1);
          p.append(input1);
          var p1 = document.createElement("p");
          p1.innerHTML="School: ";
          div.append(p1);
          var input2 = document.createElement("input");
          input2.type="text";
          input2.name="edu_school"+(countEdu+1);
          input2.className="school";
          p1.append(input2);
          console.log("Adding education "+countEdu);
          $('.school').autocomplete({ source: "school.php" });
        });
      });
</script>
