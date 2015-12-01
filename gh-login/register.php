<!-- register.php -->

<?php
  session_start();
	$loggedIn = empty($_SESSION['loggedin']) ? false : $_SESSION['loggedin'];
	if (!$loggedIn) {
		header("Location: users.php");
		exit;
	}
?>

<!DOCTYPE html>
<html>
  <head>
    <link href='stylesheet.css' type='text/css' rel='stylesheet'>
  </head>
  <body>
    <div id='menu'>
      <a id="homeButton" href="adminpage.php">HOME</a>
      <a id="registerButton" href="register.php">USERS</a>
      <form action='logout.php' method='POST'>
        <button type='submit' id='logoutButton' name='button' value='logout'>Log out</button>
      </form>
    </div>
    <div id='content' class='content'>
      <form id='registerForm' action='users.php' method='POST'>
        <p>Enter a username and password to register a user</p>
        <input type=hidden name='requestFromRegister' value=true>
        <div id='inputTextDiv'>
          <input type='text' id='user' name='username' placeholder='username'><br>
          <input type='password' id='pass' name='password' placeholder='password'><br>
        </div>
        <div id='inputButtonDiv'>
          <button type='submit' id='formButton' name='button' value='register'>Register</button>
        </div>
        <?php
          if($error != ''){
            echo "<p>".$error."</p>";
          }
          if($success != ''){
            echo "<p>".$success."</p>";
          }
        ?>
      </form>
    </div>
  </body>
</html>
