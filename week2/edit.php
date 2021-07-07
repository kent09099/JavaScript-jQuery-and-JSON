<?php
session_start();
require_once "pdo.php";
require_once "bootstrap.php";


if(!isset($_SESSION["user_id"])){
  die("Not logged in");
}

if ( isset($_POST['first_name']) && isset($_POST['email'])
     && isset($_POST['last_name']) && isset($_POST['headline'])
     && isset($_POST['summary'])) {

    print_r($_POST);
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

    $_SESSION['success'] = 'Profile updated';
    header( 'Location: index.php' ) ;
    return;
  }

  // Guardian: Make sure that user_id is present
  if ( ! isset($_GET['profile_id']) ) {
    $_SESSION['error'] = "Missing profile_id";
    header('Location: index.php');
    return;
  }

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
<textarea name="summary" rows="8" cols="80"><?= $su ?></textarea>
<input type="hidden" name="profile_id" value="<?= $profile_id ?>">
<p>
<input type="submit" value="Save">
<a href="index.php">Cancel </a>
</p>
</form>
</div>
</body>
