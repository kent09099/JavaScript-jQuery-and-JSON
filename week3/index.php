<?php
session_start();
require_once "bootstrap.php";
require_once "pdo.php";
?>
<html>
<head>
  <title>kruirona99 resume Registry 394b41c8</title>
</head>
<body>
<div class="container">
<h1>kruirona99's Resume Registry</h1>
<?php
if ( isset($_SESSION["success"]) ) {
    echo('<p style="color:green">'.$_SESSION["success"]."</p>\n");
    unset($_SESSION["success"]);
}

if ( isset($_SESSION['error']) ) {
    echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
    unset($_SESSION['error']);
}

if(isset($_SESSION["user_id"])){
  echo("<p><a href='logout.php'>Logout</a></p>");
  echo('<table border="1">'."\n");
  $stmt = $pdo->query("SELECT profile_id, first_name, last_name, headline FROM Profile");
  echo('<thead><tr>
        <th>Name</th>
        <th>Headline</th>
        <th>Action</th>
        </tr></thead>');
  while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {

      echo "<tr><td>";
      echo("<a href='view.php?profile_id=".$row['profile_id']."''>".htmlentities($row['first_name']." ".$row['last_name'])."</a>");
      echo("</td><td>");
      echo(htmlentities($row['headline']));
      echo("</td><td>");
      echo('<a href="edit.php?profile_id='.$row['profile_id'].'">Edit</a> / ');
      echo('<a href="delete.php?profile_id='.$row['profile_id'].'">Delete</a>');
      echo("</td></tr>\n");
    }
  echo("</table>");
  echo("<p><a href='add.php'>Add New Entry</a></p>");

}else{
  echo("<p><a href='login.php'>Please log in</a></p>");
  echo('<table border="1">'."\n");
  echo('<thead><tr>
        <th>Name</th>
        <th>Headline</th>
        </tr></thead>');
  $stmt = $pdo->query("SELECT first_name, last_name, headline FROM Profile");
  while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {

      echo "<tr><td>";
      echo("<a>".htmlentities($row['first_name'].$row['last_name'])."</a>");
      echo("</td><td>");
      echo(htmlentities($row['headline']));
      echo("</td><td>");
    }
    echo("</table>");
}
?>
</div>
