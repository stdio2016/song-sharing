<?php
require_once 'b.php';
if (!isset($_REQUEST['back'])) {
  $_REQUEST['back'] = 'index.php';
}
$mypath = 'register.php?back='.urlencode($_REQUEST['back']);
if (logged_in()) {
  header('Location:' . $_REQUEST['back']);
  exit();
}
if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['passwordAgain'])) {
  $test = preg_match("/^[A-Za-z0-9]{1,20}$/", $_POST['username']);
  if ($test != 1) {
    $_SESSION['songs/msg'] = $trans['username restriction'];
  }
  else if ($_POST['password'] !== $_POST['passwordAgain']) {
    $_SESSION['songs/msg'] = $trans['password differs'];
  }
  else {
    $sql = "SELECT * FROM account WHERE name=:name";
    $query = $db->prepare($sql);
    $query->execute(array(
      ':name' => $_POST['username']
    ));
    $f = $query->fetch();
    if (is_array($f)) {
      $_SESSION['songs/msg'] = $trans['user registered'];
    }
    else {
      $sql = "INSERT INTO account(name,password) VALUES(:name, :hash)";
      $query = $db->prepare($sql);
      $f = $query->execute(array(
        ':name' => $_POST['username'],
        ':hash' => password_hash($_POST['password'], PASSWORD_DEFAULT)
      ));
      if ($f === True) {
        $_SESSION['songs/user'] = $_POST['username'];
        header('Location:' . $_REQUEST['back']);
        exit();
      }
      $_SESSION['songs/msg'] = 'Internal error has occured';
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
$title = $trans['register'];
$login_page = True;
?>
<?php include_once 'header.php'; ?>
<h1><?=$title?></h1>
<hr>
<form action="register.php" method="POST">
  <p>
    <label for="username"><?=$trans['username:']?></label>
    <input name="username" maxlength="20">
  </p>
  <p>
    <label for="password"><?=$trans['password:']?></label>
    <input name="password" type='password' maxlength="20">
  </p>
  <p>
    <label for="passwordAgain"><?=$trans['password again:']?></label>
    <input name="passwordAgain" type='password' maxlength="20">
  </p>
  <p>
    <button type="submit"><?=$trans['register']?></button>
  </p>
  <input type="hidden" name="back" value="<?=htmlspecialchars($_GET['back'])?>">
</form>
<hr>
<?php include_once 'footer.php';
