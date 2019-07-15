<?php
$mypath = 'play.php?id=' . $_GET['id'];
require_once 'b.php';
$sql = "SELECT name, `date`, file, description FROM sounds where id = :id";
$query = $db->prepare($sql);
if (!isset($_GET['id'])) {
  header('Location: index.php');
  exit();
}
$query->execute(array(':id' => $_GET['id']));
$f = $query->fetch();
$name = htmlspecialchars($f['name']);
$user = isset($_SESSION['songs/user']) ? $_SESSION['songs/user'] : "";
?>
<!DOCTYPE html>
<html lang="<?=$lang?>">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $f ? $name : $trans['recording not found'] ?></title>
<link rel="stylesheet" href="<?=BASE_PATH?>/css/main.css">
<script src="<?=BASE_PATH?>/js/validate.js" charset="utf-8"></script>
<script src="<?=BASE_PATH?>/lang/<?=$lang?>.js" charset="utf-8"></script>
</head>
<body>
<?php include 'login.php'; ?>
<?php if (!$f) {
  http_response_code(404);
?>
<h1><?=$trans['recording not found']?></h1>
<p><?=$trans['recording not found reason']?></p>
<?php } else { ?>
<h1><?= $name ?></h1>
<span class="date"><?= $f['date'] ?></span>
<hr>
<p><?= $f['description'] ?></p>
<audio controls>
  <source src="<?=BASE_PATH?>/files/<?= $f['file'] ?>.ogg">
  <source src="<?=BASE_PATH?>/files/<?= $f['file'] ?>.m4a">
  <source src="<?=BASE_PATH?>/files/<?= $f['file'] ?>.wav" type="audio/wav">
  <source src="<?=BASE_PATH?>/files/<?= $f['file'] ?>.mp3" type="audio/mpeg">
  <!-- sorry IE users -->
  <embed src="<?=BASE_PATH?>/files/<?= $f['file'] ?>.mp3" width='250' height='40'>
</audio>
<!--[if gte IE 8]><!-->
<h2><?=$trans['comment']?></h2>
  <?php if (isset($_SESSION['songs/user'])) { ?>

<form id='cmt' action="makeComment.php" method="post" onsubmit="return checkNoEmptyComment(event)">
  <input name='song' type="hidden" value='<?= $_GET['id'] ?>'>
  <textarea name='comment' maxlength="1000"></textarea><br>
  <button type="submit"><?=$trans['ok']?></button>
</form>
  <?php } else { ?>

<p><?=$trans['you must login to make comment']?></p>
  <?php } ?>
  
<!--<![endif]-->
<!--[if IE]>
<p><?=$trans['you are using old and unsupported ie']?></p>
<![endif]-->
<div id="lstComment">
</div>
<script src="<?=BASE_PATH?>/js/asyncComment.js" charset="utf-8"></script>
<?php } ?>
<hr>
<?php require 'c.php';
