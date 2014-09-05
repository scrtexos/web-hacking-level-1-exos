<?php
if(isset($_GET['logout'])){
  setcookie('my_session','');
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


if(isset($_POST['username']) && isset($_POST['password'])){
  create_user_if_not_exist($_POST['username'], $_POST['password']);
  $db = new SQLite3($dbname);
  $safe_username = $db->escapeString($_POST['username']);
  $password_hash = hash("sha256", $_POST['password']);
  $query = "SELECT id FROM users WHERE username='".$safe_username."' and password='".$password_hash."'";
  $result = (int)$db->querySingle($query);
  if($result != 0){
    setcookie('my_session', $_POST['username'].'|'.(time()+300).'|'.md5($_POST['username'].'|'.(time()+300)), time()+300);
  }
  $db->close();
  header('Location: ./');
}
$logged=0;
if(isset($_COOKIE['my_session'])){
  $values = explode('|', $_COOKIE['my_session']);
  if(count($values) == 3 && (int)$values[1]> time() && md5($values[0].'|'.$values[1]) === $values[2]){
    $logged = 1;
    $username = $values[0];
  }
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
        <h1>Exercice 8 - Session management</h1>
        <p class="lead">Login with admin account.</p>
        <?php
          if($logged==1){
        ?>
        <p>Hello <?php echo htmlentities($username); ?> you are connected !</p>
        <button type="button" class="btn btn-default" onclick="javascript:document.location='./?logout=1'">Logout</button>
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
