<?php
date_default_timezone_set('UTC');
$dbname = 'db/.htdb.db';

$tbl_comments = "CREATE TABLE IF NOT EXISTS comments (id INTEGER PRIMARY KEY AUTOINCREMENT UNIQUE NOT NULL, date TEXT, comment TEXT, username TEXT);";

$db = new SQLite3($dbname);
$db->exec($tbl_comments);
$db->close();

if(isset($_POST['message']) && isset($_POST['username'])){
  $db = new SQLite3($dbname);
  $safe_message = $db->escapeString($_POST['message']);
  $safe_username = $db->escapeString($_POST['username']);
  $query = "INSERT INTO comments (date, comment, username) VALUES ('".date("d/m/Y, H:i")."', '".$safe_message."', '".$safe_username."')";
  $db->exec($query);
  $db->close();
}

$db = new SQLite3($dbname);
$query = "SELECT date, comment, username from comments";
$sqlResult = $db->query($query);
$result = array();
$i = 0;

while($res = $sqlResult->fetchArray(SQLITE3_ASSOC)){ 
  $result[$i] = $res; 
  $i++;
}
$db->close();

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
        <h1>Exercice 5 - XSS</h1>
        <p class="lead">Deface the website with your beautiful banner.</p>
        <form id="my_form" method="POST" action="">
          <div class="form-group">
            <label for="username" class="col-sm-2 control-label">Username :</label>
            <input name="username" id="username" class="form-control">
          </div>
          <div class="form-group">
            <label for="message" class="col-sm-2 control-label">Message :</label>
            <textarea name="message" id="message" class="form-control" rows="3"></textarea>
          </div>
          <div class="form-group">
            <button type="submit" class="btn btn-default">Submit</button>
          </div>
        </form>
      </div>


      <div class="row">
        <?php
          for($i=0;$i<count($result);$i++){
        ?>
          <div class="col-lg-4">
            <h3><?php echo '<b>'.htmlentities($result[$i]['username']).'</b> : '.$result[$i]['date']; ?></h3>
            <p><?php echo $result[$i]['comment']; ?></p>
          </div>
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
