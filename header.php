<!DOCTYPE html>
<html lang="<?=$lang?>">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?=$title?></title>
<link rel="stylesheet" href="<?=BASE_PATH?>/css/main.css">
<script src="<?=BASE_PATH?>/lang/<?=$lang?>.js" charset="utf-8"></script>
</head>
<body>
<?php
if (isset($_SESSION['songs/msg'])) {
  echo "<p>" . $_SESSION['songs/msg'] . "</p>\n";
  unset($_SESSION['songs/msg']);
}
if (isset($_SESSION['songs/user'])) { ?>
<form action="logout.php">
  <p>
    <?=$trans['welcome,']?><span><?=$_SESSION['songs/user']?></span>
    <button type="submit"><?=$trans['logout']?></button>
  </p>
</form>
<script>
var username = "<?=$_SESSION['songs/user']?>";
</script>
<?php } else if (!isset($login_page)) { ?>
<script>
var username = "";
</script>
<a href="login.php?back=<?=htmlspecialchars(urlencode($_SERVER['REQUEST_URI']))?>"><?=$trans['login']?></a>
<a href="register.php?back=<?=htmlspecialchars(urlencode($_SERVER['REQUEST_URI']))?>"><?=$trans['register']?></a>
<?php }
if (!isset($mypath)) $mypath = 'index.php';
$mypath = htmlspecialchars($mypath);
?>
<p>
  <a href='<?=BASE_PATH.'/zh-tw/'.$mypath?>'>繁體中文</a>
  <a href='<?=BASE_PATH.'/zh/'.$mypath?>'>简体中文</a>
  <a href='<?=BASE_PATH.'/en/'.$mypath?>'>English</a>
</p>
<script>
var BASE_PATH = "<?=BASE_PATH?>";
</script>
