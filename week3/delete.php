<?php
require_once "pdo.php";
require_once "bootstrap.php";
session_start();

if ( isset($_POST['delete']) && isset($_POST['profile_id']) ) {
    $sql = "DELETE FROM Profile WHERE profile_id = :profile_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(':profile_id' => $_GET['profile_id']));
    $_SESSION['success'] = 'Profile deleted';
    header( 'Location: index.php' ) ;
    return;
}

// Guardian: Make sure that user_id is present
if ( ! isset($_GET['profile_id']) ) {
  $_SESSION['error'] = "Missing profile_id";
  header('Location: index.php');
  return;
}

$stmt = $pdo->prepare("SELECT first_name, last_name FROM Profile where profile_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['error'] = 'Bad value for profile_id';
    header( 'Location: index.php' ) ;
    return;
}

?>

<head>
  <title>kruirona99 Profile Delete</title>
</head>
<body>
  <div class="container">
    <h1>Deleting Profile</h1>
    <?php
    echo ("<p>First Name: ". $row['first_name'] . "</p>");
    echo ("<p>Last Name: ". $row['last_name'] . "</p>");
     ?>
    <form method="post">
    <input type="hidden" name="profile_id" value="<?= $_GET['profile_id'] ?>">
    <input type="submit" value="Delete" name="delete">
    <a href="index.php">Cancel</a>
  </form>
  </div>
</body>
