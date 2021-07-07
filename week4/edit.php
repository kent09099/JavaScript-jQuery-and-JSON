<?php
session_start();
require_once "pdo.php";
require_once "bootstrap.php";
require_once "utils.php";


if (!isset($_SESSION["user_id"])){
  die("ACCESS DENIED");
}


if ( isset($_POST['first_name']) && isset($_POST['email'])
     && isset($_POST['last_name']) && isset($_POST['headline'])
     && isset($_POST['summary'])) {

    // Data validation
    if($_POST['first_name'] == false || $_POST['last_name'] == false ||
       $_POST['email'] == false || $_POST['headline'] == false ||
       $_POST['summary'] == false){
         $_SESSION["error"] = 'All fields are required';
         header("Location: edit.php?profile_id=" . $_POST["profile_id"]);
         return;
       }

    if ( strpos($_POST['email'],'@') === false ) {
        $_SESSION['error'] = 'Email address must contain @';
        header("Location: edit.php?profile_id=" . $_POST["profile_id"]);
        return;
    }

    $sql = "UPDATE Profile SET first_name = :first_name,
            email = :email, last_name = :last_name,
            headline = :headline, summary = :summary
            WHERE profile_id = :profile_id";

    $stmt = $pdo->prepare($sql);

    $stmt->execute(array(
        ':first_name' => $_POST['first_name'],
        ':last_name' => $_POST['last_name'],
        ':email' => $_POST['email'],
        ':headline' => $_POST['headline'],
        ':summary' => $_POST['summary'],
        ':profile_id' => $_GET['profile_id']));

      $valPos = validatePos();
      if (is_string($valPos)){
        $_SESSION["error"] = $valPos;
        header("Location: edit.php?profile_id=".$_REQUEST["profile_id"]);
        return;
      }

      $valEdu = validateEdu();
      if (is_string($valEdu)){
        $_SESSION["error"] = $valEdu;
        header("Location: edit.php?profile_id=".$_REQUEST["profile_id"]);
        return;
      }

      #positionの部分を一度全消去した後、更新
      $stmt = $pdo->prepare('DELETE FROM Position WHERE profile_id=:pid');
      $stmt->execute(array( ':pid' => $_REQUEST['profile_id']));
      insertPositions($pdo, $_REQUEST["profile_id"]);

      #educationsも同様に処理する
      $stmt = $pdo->prepare('DELETE FROM Education WHERE profile_id=:pid');
      $stmt->execute(array( ':pid' => $_REQUEST['profile_id']));
      insertEducations($pdo, $_REQUEST["profile_id"]);

      $_SESSION['success'] = 'Profile updated';
      header("Location: index.php");
      return;
  }

  // Guardian: Make sure that user_id is present
  if ( ! isset($_GET['profile_id']) ) {
    $_SESSION['error'] = "Missing profile_id";
    header('Location: index.php');
    return;
  }

#データベースからProfileの情報を持ってくる
  $stmt = $pdo->prepare("SELECT * FROM Profile where profile_id = :xyz");
  $stmt->execute(array(":xyz" => $_GET['profile_id']));
  #$stmt = $pdo->query("SELECT * FROM Profile WHERE profile_id = ".$_GET['profile_id']);
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  if ( $row === false ) {
      $_SESSION['error'] = 'Bad value for profile_id';
      header( 'Location: index.php' ) ;
      return;
  }

$fn = $row["first_name"];
$ln = $row["last_name"];
$em = $row["email"];
$hl = $row["headline"];
$su = $row["summary"];
$profile_id = $_GET["profile_id"];

#データベースからpositionsの情報を持ってくる
$positions = loadPos($pdo, $profile_id);
$educations = loadEdu($pdo, $profile_id);
?>

<head>
  <title>kruirona99 Profile for UMSI</title>
</head>
<body>
<div class="container">
<h1>Editing Profile for UMSI</h1>

<?php
// Flash pattern
if ( isset($_SESSION['error']) ) {
    echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
    unset($_SESSION['error']);
}

?>

<form method="post">
<p>First Name:
<input type="text" name="first_name" value="<?= $fn ?>" size="60"/></p>
<p>Last Name:
<input type="text" name="last_name" value="<?= $ln ?>" size="60"/></p>
<p>Email:
<input type="text" name="email" value="<?= $em ?>" size="30"/></p>
<p>Headline:<br/>
<input type="text" name="headline" value="<?= $hl ?>" size="80"/></p>
<p>Summary:<br/>
<textarea name="summary" rows="8" cols="80"><?= $su ?></textarea></p>
<input type="hidden" name="profile_id" value="<?= $profile_id ?>">

<p>
  Education: <input type="submit" id="addEdu" value="+">
</p>
<div id="edu_fields">
<?php
  $countEdu = 0;
  foreach($educations as $education){
    echo ('<div id="edu'. $education["rank"]. '"><p>Year: <input type="text" name="edu_year'. $education["rank"]. '"
    value="'. $education["year"] .'" />
    <input type="button" value="-" onclick="$(\'#edu'. $education["rank"]. '\').remove();return false;"></p>
    <p>School: <input type="text" size="80" name="edu_school'.$education["rank"].'" class="school" value="'.htmlentities($education["name"]).'" /></p></div>');
    $countEdu = $education["rank"];
  }
?>
</div>

<p>
Position: <input type="submit" id="addPos" value="+">
</p>
<div id="position_fields">
<?php
 #print_r($positions);
 $countPos = 0;
 foreach($positions as $position){
   echo ('<div id="position'. $position["rank"]. '"><p>Year: <input type="text" name="year'. $position["rank"]. '"
   value="'. $position["year"] .'" />
   <input type="button" value="-" onclick="$(\'#position'. $position["rank"]. '\').remove();return false;"></p>
   <textarea name="desc'. $position["rank"]. '" rows="8" cols="80">'. htmlentities($position["description"]) .'</textarea>\</div>');
   $countPos = $position["rank"];
 }
?>
</div>

<p>
<input type="submit" value="Save">
<a href="index.php">Cancel </a>
</p>
</form>
<script>
countPos = <?= $countPos ?>;
countEdu = <?= $countEdu ?>;
// http://stackoverflow.com/questions/17650776/add-remove-html-inside-div-using-javascript
$(document).ready(function(){
    window.console && console.log('Document ready called');
    $('#addPos').click(function(event){
        // http://api.jquery.com/event.preventdefault/
        event.preventDefault();
        if ( countPos >= 9 ) {
            alert("Maximum of nine position entries exceeded");
            return;
        }
        countPos++;
        window.console && console.log("Adding position "+countPos);
        $('#position_fields').append(
            '<div id="position'+countPos+'"> \
            <p>Year: <input type="text" name="year'+countPos+'" value="" /> \
            <input type="button" value="-" \
                onclick="$(\'#position'+countPos+'\').remove();return false;"></p> \
            <textarea name="desc'+countPos+'" rows="8" cols="80"></textarea>\
            </div>');
    });

    $('#addEdu').click(function(event){
          event.preventDefault();
          if ( countEdu >= 9 ) {
                alert("Maximum of nine education entries exceeded");
                return;
            }
            countEdu++;
            window.console && console.log("Adding education "+countEdu);

            $('#edu_fields').append(
                '<div id="edu'+countEdu+'"> \
                <p>Year: <input type="text" name="edu_year'+countEdu+'" value="" /> \
                <input type="button" value="-" onclick="$(\'#edu'+countEdu+'\').remove();return false;"><br>\
                <p>School: <input type="text" size="80" name="edu_school'+countEdu+'" class="school" value="" />\
                </p></div>'
            );

            $('.school').autocomplete({
                source: "school.php"
            });

        });

});
</script>
<script id="edu_template" type="text">
  <div id="edu@COUNT@">
     <p>
      Year: <input type="text" name="edu_year@COUNT@" value="" />
      <input type="button" value="-" onclick="$('#edu@COUNT@').remove();return false;">
      <p>School: <input type="text" size="80" name="edu_school@COUNT@" class="school" value="" /></p>
     </p>
  </div>
</script>
</div>
</body>
