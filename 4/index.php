<?php
$quotes = array();
array_push($quotes,
	array(
		"author" => "C.A.R. Hoare",
		"content" => "There are two ways of constructing a software design: One way is to make it so simple that there are obviously no deficiencies, and the other way is to make it so complicated that there are no obvious deficiencies. The first method is far more difficult."
	),
	array(
		"author" => "Edsger Dijkstra",
		"content" => "If debugging is the process of removing software bugs, then programming must be the process of putting them in."
	),
	array(
		"author" => "Bill Gates",
		"content" => "Measuring programming progress by lines of code is like measuring aircraft building progress by weight."
	),
	array(
		"author" => "Fred Brooks",
		"content" => "Nine people can’t make a baby in a month."
	),
	array(
		"author" => "Brian W. Kernighan",
		"content" => "Debugging is twice as hard as writing the code in the first place. Therefore, if you write the code as cleverly as possible, you are, by definition, not smart enough to debug it."
	),
	array(
		"author" => "Martin Golding",
		"content" => "Always code as if the guy who ends up maintaining your code will be a violent psychopath who knows where you live."
	),
	array(
		"author" => "Bjarne Stroustrup",
		"content" => "C makes it easy to shoot yourself in the foot; C++ makes it harder, but when you do, it blows away your whole leg."
	),
	array(
		"author" => "Richard Pattis",
		"content" => "When debugging, novices insert corrective code; experts remove defective code."
	),
	array(
		"author" => "Eric S. Raymond",
		"content" => "Computer science education cannot make anybody an expert programmer any more than studying brushes and pigment can make somebody an expert painter."
	),
	array(
		"author" => "Linus Torvalds",
		"content" => "Most good programmers do programming not because they expect to get paid or get adulation by the public, but because it is fun to program."
	),
	array(
		"author" => "Martin Fowler",
		"content" => "Any fool can write code that a computer can understand. Good programmers write code that humans can understand."
	),
	array(
		"author" => "Steve McConnell",
		"content" => "Good code is its own best documentation. As you’re about to add a comment, ask yourself, ‘How can I improve the code so that this comment isn’t needed?"
	),
	array(
		"author" => "Larry Wall",
		"content" => "The problem with using C++ … is that there’s already a strong tendency in the language to require you to know everything before you can do anything."
	),
	array(
		"author" => "Donald Knuth",
		"content" => "People think that computer science is the art of geniuses but the actual reality is the opposite, just many people doing things that build on each other, like a wall of mini stones."
	),
	array(
		"author" => "Ken Thompson",
		"content" => "One of my most productive days was throwing away 1000 lines of code."
	),
	array(
		"author" => "Alan Kay",
		"content" => "Most software today is very much like an Egyptian pyramid with millions of bricks piled on top of each other, with no structural integrity, but just done by brute force and thousands of slaves."
	),
	array(
		"author" => "Ralph Johnson",
		"content" => "Before software can be reusable it first has to be usable."
	),
	array(
		"author" => "Michael Sinz",
		"content" => "Programming is like sex. One mistake and you have to support it for the rest of your life."
	),
	array(
		"author" => "Gerald Weinberg",
		"content" => "If builders built buildings the way programmers wrote programs, then the first woodpecker that came along wound destroy civilization."
	)
);

$secret_quotes = array();
array_push($secret_quotes,
	array(
		"author" => "Edward Snowden",
		"content" => "* SECRET * The NSA has built an infrastructure that allows it to intercept almost everything."
	),
	array(
		"author" => "Julien Assange",
		"content" => "* SECRET * You can either be informed and your own rulers, or you can be ignorant and have someone else, who is not ignorant, rule over you."
	),
	array(
		"author" => "WELL DONE",
		"content" => "* SECRET * This message should not be read by anybody..."
	)
);
session_name(session_name().'_exo4');
session_start();
if(isset($_GET['logout'])){
  unset($_SESSION['logged']);
  session_destroy();
  header('Location: ./');
}
$dbname = 'db/.htdb.db';
$admin_password = 'p8RnQlVccP3nl5SJN96SKaHZlM441jEZ';
$admin_username = 'admin';

function create_quotes_if_not_exist($username, $password, $quotes){
  global $dbname;
  $rand_quotes = array_rand($quotes,3);
  $db = new SQLite3($dbname);
  $safe_username = $db->escapeString($username);
  $password_hash = hash("sha256", $password);
  $query = "SELECT id FROM quotes WHERE username='".$safe_username."'";
  $result = (int)$db->querySingle($query);
  if($result == 0){
    $query = "INSERT INTO quotes (username, password, author, content) VALUES ('".$safe_username."', '".$password_hash."', '".$quotes[$rand_quotes[0]]['author']."', '".$quotes[$rand_quotes[0]]['content']."')";
    $db->exec($query);
    $query = "INSERT INTO quotes (username, password, author, content) VALUES ('".$safe_username."', '".$password_hash."', '".$quotes[$rand_quotes[1]]['author']."', '".$quotes[$rand_quotes[1]]['content']."')";
    $db->exec($query);
    $query = "INSERT INTO quotes (username, password, author, content) VALUES ('".$safe_username."', '".$password_hash."', '".$quotes[$rand_quotes[2]]['author']."', '".$quotes[$rand_quotes[2]]['content']."')";
    $db->exec($query);
  }
  $db->close();
}

$tbl_users = "CREATE TABLE IF NOT EXISTS quotes (id INTEGER PRIMARY KEY AUTOINCREMENT UNIQUE NOT NULL, username TEXT, password TEXT, author TEXT, content INTEGER);";

$db = new SQLite3($dbname);
$db->exec($tbl_users);
$db->close();
create_quotes_if_not_exist($admin_username, $admin_password, $secret_quotes);

if(isset($_POST['username']) && isset($_POST['password'])){
  create_quotes_if_not_exist($_POST['username'], $_POST['password'], $quotes);
  $db = new SQLite3($dbname);
  $safe_username = $db->escapeString($_POST['username']);
  $password_hash = hash("sha256", $_POST['password']);
  $query = "SELECT id FROM quotes WHERE username='".$safe_username."' and password='".$password_hash."'";
  $result = (int)$db->querySingle($query);
  if($result != 0){
    $_SESSION['logged']=$_POST['username'];
  }
  $db->close();
}

if(isset($_SESSION['logged'])){
  $message=" do you want to change your password ?";
  $db = new SQLite3($dbname);
  $safe_username = $db->escapeString($_SESSION['logged']);
  $query = "SELECT id, author FROM quotes WHERE username='".$safe_username."'";
  $sqlResult = $db->query($query);
  $result = array();
  $i = 0;

  while($res = $sqlResult->fetchArray(SQLITE3_ASSOC)){ 
    $result[$i] = $res; 
    $i++;
  }
  $db->close();
}

if(isset($_GET['id'])){
  $db = new SQLite3($dbname);
  $safe_id = (int)($_GET['id']);
  $query = "SELECT author, content FROM quotes WHERE id=".$safe_id;
  $quote = @$db->querySingle($query, true);
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
        <h1>Exercice 4 - Direct object access</h1>
        <p class="lead">Discover secret informations</p>
        <?php
          if(isset($_SESSION['logged'])){
        ?>
        </div>
        <div class="row">
        	<h2><?php echo @htmlentities($quote['author']); ?></h2>
        	<p><?php echo @htmlentities($quote['content']); ?></p>
      	</div>
      	<div class="row">
        <?php
          for($i=0;$i<count($result);$i++){
        ?>
          <div class="col-lg-4">
            <h3><?php echo htmlentities($result[$i]['author']); ?></h3>
            <p><a class="btn btn-default" href="./?id=<?php echo $result[$i]['id']; ?>" role="button">View content &raquo;</a></p>
          </div>
        <?php
          }
        ?>
      </div>
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
      </div>
        <?php
          }
        ?>
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
