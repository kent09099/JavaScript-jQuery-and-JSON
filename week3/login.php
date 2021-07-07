<?php // Do not put any HTML above this line
session_start();
require_once "pdo.php";
if ( isset($_POST['cancel'] ) ) {
    // Redirect the browser to game.php
    header("Location: index.php");
    return;
}

$salt = 'XyZzy12*_';
$stored_hash = '1a52e17fa899cf40fb04cfc42e6352f1';


if(isset($_POST["email"]) && isset($_POST["pass"])){
  $check = hash('md5', $salt.$_POST['pass']);
  $stmt = $pdo->prepare('SELECT user_id, name FROM users
                        WHERE email = :em AND password = :pw');
  $stmt->execute(array( ':em' => $_POST['email'], ':pw' => $check));
  $row = $stmt->fetch(PDO::FETCH_ASSOC);

  if($row !== false){
    $_SESSION['name'] = $row['name'];
    $_SESSION['user_id'] = $row['user_id'];

    header("Location: index.php");
    return;
  }else{
    $_SESSION["error"] = "Incorrect password.";
    header("Location: login.php");
    return;
  }
}
/*
if ( isset($_POST["email"]) && isset($_POST["pass"]) ) {
    unset($_SESSION["account"]);

    if ( strlen($_POST['email']) < 1 || strlen($_POST['pass']) < 1 ) {
      $_SESSION["error"] = "Email and password are required";
      header( 'Location: login.php' ) ;
      return;

    }else if(strpos($_POST['email'], '@') === false){
      $_SESSION["error"] = "Email must have an at-sign (@)";
      header( 'Location: login.php' ) ;
      return;
    }

    $check = hash('md5', $salt.$_POST['pass']);
    // Logout current user
    if ( $check === $stored_hash) {

        $_SESSION["account"] = $_POST["email"];
        $_SESSION["success"] = "Logged in.";
        error_log("Login success ".$_POST['email']. "\n", 3, "error_log.txt");
        header( 'Location: view.php' ) ;

        return;

    } else {

        $_SESSION["error"] = "Incorrect password.";
        header( 'Location: login.php' ) ;
        error_log("Login fail ".$_POST['email']." $check" . "\n", 3, "error_log.txt");
        return;
    }
}
*/

?>
<!DOCTYPE html>
<html>
<head>
<title>kruirona99's Login Page 53e6c7d0</title>
<link rel="stylesheet"
    href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"
    integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7"
    crossorigin="anonymous">

<link rel="stylesheet"
    href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css"
    integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r"
    crossorigin="anonymous">

<link rel="stylesheet"
    href="https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css">

<script type="text/javascript">
function doValidate() {
         console.log('Validating...');
         try {
             pw = document.getElementById('id_1723').value;
             console.log("Validating pw="+pw);
             if (pw == null || pw == "") {
                 alert("Both fields must be filled out");
                 return false;
             }
             return true;
         } catch(e) {
             return false;
        }
         return false;
     }
</script>

</head>

<body>
<div class="container">
<h1>Please Log In</h1>
<?php
// Note triple not equals and think how badly double
// not equals would work here...
if (isset($_SESSION["error"])){
  echo('<p style="color:red">'.$_SESSION["error"]."</p>\n");
  unset($_SESSION["error"]);
}

?>
<form method="POST">
<label for="nam">User Name</label>
<input type="text" name="email" id="nam"><br/>
<label for="id_1723">Password</label>
<input type="password" name="pass" id="id_1723"><br/>
<input type="submit" onclick="return doValidate();" value="Log In">
<a href="index.php">Cancel</a></p>
</form>
<p>
For a password hint, view source and find a password hint
in the HTML comments.
<!-- Hint: The password is the four character sound a cat
makes (all lower case) followed by 123. -->
</p>
</div>
</body>
