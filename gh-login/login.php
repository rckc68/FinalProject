<!-- login.php -->
<?php
  session_start();

  //CHECK IF USER IS ALREADY LOGGED IN
  $loggedIn = empty($_SESSION['loggedin']) ? false : $_SESSION['loggedin'];
  if($loggedIn){
    if($_SESSION['admin'] == 1){
      header("Location: adminpage.php");
    }else{
      header("Location: userpage.php");
    }
    exit;
  }

  //HANDLE BUTTON SELECTION
  $button = empty($_POST['button']) ? '' : $_POST['button'];
  switch($button){
    case '':
      handle_startpage();
      break;
    case 'logout':
      handle_startpage();
      break;
    case 'login':
      handle_login();
      break;
    case 'register':
      handle_register();
      break;
  }

  //LOGIN
  function handle_login(){
    $username = empty($_POST['username']) ? '' : $_POST['username'];
    $password = empty($_POST['password']) ? '' : $_POST['password'];
    if($password == '' || $username==''){ //EMPTY USERNAME OR PASSWORD - PRINT ERROR
      $error = "Incorrect username or password.";
      require "index.php";
    }else{ //ELSE LOOK UP USERNAME AND VERIFY PASSWORD
      require_once("db.conf"); //MySQL CONNECTION
      $link = mysqli_connect($dbhost,$dbuser,$dbpass,$dbname) or die("Connect Error".mysqli_error($link));
      $sql = "SELECT * FROM user WHERE username='".$_POST['username']."';";
      $result = mysqli_query($link,$sql);
      $row = mysqli_fetch_array($result);
      if($row['username']==''){ //USERNAME NOT FOUND
        mysqli_close();
        $error = "Username not found.";
        require "index.php";
      }else{ //USER FOUND, VERIFY PASSWORD
        $salt = $row['salt'];
        $hash = $row['hashed_password'];
        $admin = $row['admin'];
        if(password_verify($salt.$_POST['password'],$hash)){ //LOGIN SUCCESSFUL
          $_SESSION['loggedin'] = $username;
          $_SESSION['admin'] = $admin;
          mysqli_close();
          if($admin==1){ //REDIRECT USER OR ADMIN
            header("Location: adminpage.php");
            exit;
          }else{
            header("Location: userpage.php");
            exit;
          }
        }else{ //ELSE INCORRECT PASSWORD - PRINT ERROR
          mysqli_close();
          $error = "Incorrect password.";
          require "index.php";
        }
      }
    }
  }

  //REGISTER
  function handle_register(){
    $username = empty($_POST['username']) ? '' : $_POST['username'];
    $password = empty($_POST['password']) ? '' : $_POST['password'];
    if($password == '' || $username==''){ //EMPTY USERNAME OR PASSWORD - PRINT ERROR
      $error = "Not enough characters in username or password.";
      require "adminpage.php";
    }else{
      require_once("db.conf"); //MySQL CONNECTION
      $link = mysqli_connect($dbhost,$dbuser,$dbpass,$dbname) or die("Connect Error".mysqli_error($link));
      $sql = "INSERT INTO user(username,salt,hashed_password,admin) VALUES (?,?,?,0)";
      if ($stmt = mysqli_prepare($link, $sql)){
          $salt = mt_rand();
          $hpass = password_hash($salt.$_POST['password'], PASSWORD_BCRYPT)  or die("bind param");
          mysqli_stmt_bind_param($stmt, "sss", $username, $salt, $hpass) or die("bind param");
          if(mysqli_stmt_execute($stmt)){
            mysqli_close();
            $success = "Username and password created succesfully!";
            require "adminpage.php";
          }else{
            mysqli_close();
            $error = "Error: username and password could not be created.";
            require "adminpage.php";
          }
        }else{
          mysqli_close();
          $error = "Error: username and password could not be created.";
          require "adminpage.php";
        }
    }
  }

  //FAILED LOGIN - RETURN TO LOGIN PAGE
  function handle_startpage(){
    $username = "";
    $password = "";
    require "index.php";
  }

?>
