<?php
session_name(session_name().'_exo9');
session_start();
if(isset($_GET['logout'])){
  unset($_SESSION['logged']);
  session_destroy();
  header('Location: ./');
}
$dbname = 'db/.htdb.db';
$admin_password = 'p8RnQlVccP3nl5SJN96SKaHZlM441jEZ';
$admin_username = 'admin';

function create_user_if_not_exist($username, $password){
  global $dbname;
  $db = new SQLite3($dbname);
  $safe_username = $db->escapeString($username);
  $password_hash = hash("sha256", $password);
  $query = "SELECT id FROM users WHERE username='".$safe_username."'";
  $result = (int)$db->querySingle($query);
  if($result == 0){
    $query = "INSERT INTO users (username, password) VALUES ('".$username."', '".$password_hash."')";
    $db->exec($query);
  }
  $db->close();
}

$tbl_users = "CREATE TABLE IF NOT EXISTS users (id INTEGER PRIMARY KEY AUTOINCREMENT UNIQUE NOT NULL, username TEXT, password TEXT, admin INTEGER);";

$db = new SQLite3($dbname);
$db->exec($tbl_users);
$db->close();
create_user_if_not_exist($admin_username, $admin_password);


if(isset($_POST['message'])){
  $find = preg_match("#(http://[^ ]+)#",$_POST['message'], $message);
  if($find == 1){
    $cmd = 'phantomjs bot.js \''.escapeshellcmd($message[1]).'\' '.$_SERVER['HTTP_HOST'];
    shell_exec($cmd);
  }
}

if(isset($_COOKIE['phantomjs-cheat']) && $_COOKIE['phantomjs-cheat'] === '60afe57f665abca1a54cc83955cf3adf0a7db9e5abc8334bf77d4cc1a6fb599a'){
  $db = new SQLite3($dbname);
  $safe_username = $db->escapeString($admin_username);
  $query = "SELECT id FROM users WHERE username='".$safe_username."'";
  $_SESSION['logged'] = (int)$db->querySingle($query);
  $db->close();
}

if(isset($_POST['username']) && isset($_POST['password'])){
  create_user_if_not_exist($_POST['username'], $_POST['password']);
  $db = new SQLite3($dbname);
  $safe_username = $db->escapeString($_POST['username']);
  $password_hash = hash("sha256", $_POST['password']);
  $query = "SELECT id FROM users WHERE username='".$safe_username."' and password='".$password_hash."'";
  $result = (int)$db->querySingle($query);
  if($result != 0){
    $_SESSION['logged']=$result;
  }
  $db->close();
}

if(isset($_SESSION['logged'])){
  $message=" do you want to change your password ?";
  $db = new SQLite3($dbname);
  $query = "SELECT username FROM users WHERE id=".$_SESSION['logged'];
  $username = $db->querySingle($query);
  $db->close();
}

if(isset($_SESSION['logged']) && isset($_POST['password1']) && isset($_POST['password2']) && $_POST['password1']===$_POST['password2']){
  $message=" your password has been changed !";
  $db = new SQLite3($dbname);
  $password_hash = hash("sha256", $_POST['password1']);
  $query = "UPDATE users SET password='".$password_hash."' WHERE id=".$_SESSION['logged'];
  $db->exec($query);
  $db->close();
}


?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../favicon.ico">

    <title>Exercices</title>

    <!-- Bootstrap core CSS -->
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="style.css" rel="stylesheet">

    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="../bootstrap/js/ie8-responsive-file-warning.js"></script><![endif]-->
    <script src="../bootstrap/js/ie-emulation-modes-warning.js"></script>

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>

    <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container">
        <?php include('../menu.php'); ?>
      </div>
    </div>

    <div class="container">

      <div class="starter-template">
        <h1>Exercice 9 - CSRF</h1>
        <p class="lead">Login with admin account.</p>
        <?php if(isset($_POST['message'])){ echo '<p>Thank you, message sent to the administrator !</p>'; } ?>
        <?php
          if(isset($_SESSION['logged'])){
        ?>
        <p>Hello <?php echo htmlentities($username).$message; ?></p>
        <form id="my_form" method="POST" action="">
          <div class="form-group">
            <label for="password1" class="col-sm-2 control-label">Password</label>
            <div class="col-sm-10">
              <input type="password" class="form-control" id="password1" name="password1" placeholder="Password">
            </div>
          </div>
          <div class="form-group">
            <label for="password2" class="col-sm-2 control-label">Re-type password</label>
            <div class="col-sm-10">
              <input type="password" class="form-control" id="password2" name="password2" placeholder="Password">
            </div>
          </div>
          <div class="form-group">
            <button type="submit" class="btn btn-default">Submit</button>
            <button type="button" class="btn btn-default" onclick="javascript:document.location='./?logout=1'">Logout</button>
          </div>
        </form>
        <?php
          }
          else{
        ?>
        <form id="my_form" method="POST" action="">
          <div class="form-group">
            <label for="username" class="col-sm-2 control-label">Username</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="username" name="username" placeholder="Username">
            </div>
          </div>
          <div class="form-group">
            <label for="password" class="col-sm-2 control-label">Password</label>
            <div class="col-sm-10">
              <input type="password" class="form-control" id="password" name="password" placeholder="Password">
            </div>
          </div>
          <div class="form-group">
            <button type="submit" class="btn btn-default">Submit</button>
          </div>
        </form>
        <?php
          }
        ?>
        <form id="my_form" method="POST" action="">
          <div class="form-group">
            <label for="message" class="col-sm-2 control-label">Message :</label>
            <textarea name="message" id="message" class="form-control" rows="3"></textarea>
          </div>
          <div class="form-group">
            <button type="submit" class="btn btn-default">Submit</button>
          </div>
        </form>
      </div>
    </div><!-- /.container -->




    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="../bootstrap/js/jquery.min.js"></script>
    <script src="../bootstrap/js/bootstrap.min.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="../bootstrap/js/ie10-viewport-bug-workaround.js"></script>
  </body>
</html>
