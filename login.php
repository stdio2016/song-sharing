<?php
require_once 'b.php';
if (!isset($_REQUEST['back'])) {
  $_REQUEST['back'] = 'index.php';
}
$mypath = 'login.php?back='.urlencode($_REQUEST['back']);
if (logged_in()) {
  header('Location:' . $_REQUEST['back']);
  exit();
}
if (isset($_POST['username']) && isset($_POST['password'])) {
  $test = preg_match("/^[A-Za-z0-9]{1,20}$/", $_POST['username']);
  $testP = preg_match("/^[A-Za-z0-9]{0,20}$/", $_POST['password']);
  if ($test != 1) {
    $_SESSION['songs/msg'] = $trans['username restriction'];
  }
  else if ($testP != 1) {
    $_SESSION['songs/msg'] = $trans['password restriction'];
  }
  else {
    $sql = "SELECT * FROM account WHERE name=:name";
    $query = $db->prepare($sql);
    $query->execute(array(
      ':name' => $_POST['username']
    ));
    $f = $query->fetch(); 
    if (is_array($f) && password_verify($_POST['password'], $f['password'])) {
      $_SESSION['songs/user'] = $_POST['username'];
      header('Location:' . $_REQUEST['back']);
      exit();
    }
    else {
      $_SESSION['songs/msg'] = $trans['user or pass error'];
    }
  }
  header('Location:' . $mypath);
  exit();
}
if ($_SERVER['REQUEST_METHOD'] != 'GET') {
  echo 'Bad request!!!';
  http_response_code(400);
  exit();
}
$title = $trans['login'];
$login_page = True;
?>
<?php include_once 'header.php'; ?>
<h1><?=$title?></h1>
<hr>
<form action="login.php" method="POST">
  <p>
    <label for="username"><?=$trans['username:']?></label>
    <input name="username" maxlength="20">
  </p>
  <p>
    <label for="password"><?=$trans['password:']?></label>
    <input name="password" type='password' maxlength="20">
  </p>
  <p>
    <button type="submit"><?=$trans['login']?></button>
  </p>
  <input type="hidden" name="back" value="<?=htmlspecialchars($_GET['back'])?>">
</form>
<p>
  <?=$trans['no account? please']?>
  <a href="register.php?back=<?=htmlspecialchars(urlencode($_GET['back']))?>"><?=$trans['register']?></a>
</p>
<hr>
<?php include_once 'footer.php';
