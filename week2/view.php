<?php
  session_start();
  require_once "pdo.php";
  require_once "bootstrap.php";

  if(!isset($_SESSION["user_id"])){
    die("Not logged in");
  }

$stmt = $pdo->query("SELECT profile_id, first_name, last_name, email, headline, summary FROM Profile WHERE profile_id = ".$_GET['profile_id']);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

?>

<head>
  <title>kruirona99 resume Registry</title>
</head>
<body>
  <div class = "container">
    <h1>Profile information</h1>
    <?php
     echo ("<p>First Name: ". $row["first_name"]. "</p>");
     echo ("<p>Last Name: ". $row["last_name"]. "</p>");
     echo ("<p>Email: ". $row["email"]. "</p>");
     echo ("<p>Headline</p>");
     echo ("<p>".$row["headline"]."</p>");
     echo ("<p>Summary</p>");
     echo ("<p>".$row["summary"]."</p>");
    ?>

  <p><a href="index.php">Done</a</p>
  </div>
</body>
