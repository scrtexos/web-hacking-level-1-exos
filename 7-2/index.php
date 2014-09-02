<?php
$dbname = 'db/.htdb.db';
$article1_title = 'Barbados Blackbelly sheep';
$article1_text = 'The Barbados Blackbelly sheep is a breed of domestic sheep that was developed in the Caribbean. Although it is likely the Barbados Blackbelly has African ancestry, there seems to be clear evidence that the breed, as seen today, was developed by the people on the island from sheep brought by ships fairly early in the period after Europeans first arrived. This breed is raised primarily for meat.';
$article2_title = 'Merino';
$article2_text = 'The Merino is an economically influential breed of sheep prized for its wool. The breed is originally from Turkey and central Spain (Castille), and its wool was highly valued already in the Middle Ages. Today, Merinos are still regarded as having some of the finest and softest wool of any sheep. Poll Merinos have no horns (or very small stubs, known as scurs), and horned Merino rams have long, spiral horns which grow close to the head.';
$article3_title = 'Lincoln sheep';
$article3_text = 'The Lincoln, sometimes called the Lincoln Longwool, is a breed of sheep from England. The Lincoln is the largest British sheep, developed specifically to produce the heaviest, longest and most lustrous fleece of any breed in the world. Great numbers were exported to many countries to improve the size and wool quality of their native breeds. The versatile fleece is in great demand for spinning, weaving and many other crafts.';
$secret='This is a secret';

$article['title']='Sheep';
$article['text']='Sheep (Ovis aries) are quadrupedal, ruminant mammals typically kept as livestock. Like all ruminants, sheep are members of the order Artiodactyla, the even-toed ungulates. Although the name "sheep" applies to many species in the genus Ovis, in everyday usage it almost always refers to Ovis aries. Numbering a little over one billion, domestic sheep are also the most numerous species of sheep. An adult female sheep is referred to as a ewe (/juː/), an intact male as a ram or occasionally a tup, a castrated male as a wether, and a younger sheep as a lamb.';

function create_article_if_not_exist($title, $text){
  global $dbname;
  $db = new SQLite3($dbname);
  $safe_title = $db->escapeString($title);
  $safe_text = $db->escapeString($text);
  $query = "SELECT id FROM articles WHERE title='".$safe_title."'";
  $result = (int)$db->querySingle($query);
  if($result == 0){
    $query = "INSERT INTO articles (title, text) VALUES ('".$safe_title."', '".$safe_text."')";
    $db->exec($query);
  }
  $db->close();
}

$tbl_articles = "CREATE TABLE IF NOT EXISTS articles (id INTEGER PRIMARY KEY AUTOINCREMENT UNIQUE NOT NULL, title TEXT, text TEXT);";
$tbl_secret = "CREATE TABLE IF NOT EXISTS secrets (id INTEGER PRIMARY KEY AUTOINCREMENT UNIQUE NOT NULL, secret TEXT);";

$db = new SQLite3($dbname);
$db->exec($tbl_articles);
$db->exec($tbl_secret);
$query = "SELECT id FROM secrets WHERE secret='".$secret."'";
$result = (int)$db->querySingle($query);
if($result == 0){
  $query = "INSERT INTO secrets (secret) VALUES ('".$secret."')";
  $db->exec($query);
}
$db->close();


create_article_if_not_exist($article1_title, $article1_text);
create_article_if_not_exist($article2_title, $article2_text);
create_article_if_not_exist($article3_title, $article3_text);


$db = new SQLite3($dbname);
$query = "SELECT id, title, text from articles";
$sqlResult = $db->query($query);
$result = array();
$i = 0;

while($res = $sqlResult->fetchArray(SQLITE3_ASSOC)){ 
  $result[$i] = $res; 
  $i++;
}
$db->close();

if(isset($_GET['id'])){
  $db = new SQLite3($dbname);
  $safe_id = $db->escapeString($_GET['id']);
  $query = "SELECT title, text FROM articles WHERE id=".$safe_id;
  $article = @$db->querySingle($query, true);
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
        <h1>Exercice 7 bis - Injection SQL</h1>
        <p class="lead">Dump secret.</p>
      </div>
      <div class="row">
        <h2><?php echo @htmlentities($article['title']); ?></h2>
        <p><?php echo @htmlentities($article['text']); ?></p>
      </div>
      <div class="row">
        <?php
          for($i=0;$i<count($result);$i++){
        ?>
          <div class="col-lg-4">
            <h3><?php echo htmlentities($result[$i]['title']); ?></h3>
            <p><?php echo htmlentities(substr($result[$i]['text'],0,100)).'...'; ?></p>
            <p><a class="btn btn-default" href="./?id=<?php echo $result[$i]['id']; ?>" role="button">View details &raquo;</a></p>
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
