<?php
$mypath = 'play.php?id=' . $_GET['id'];
require_once 'b.php';
$sql = "SELECT name, `date`, file, description, user FROM sounds where id = :id";
$query = $db->prepare($sql);
if (!isset($_GET['id'])) {
  header('Location: index.php');
  exit();
}
$query->execute(array(':id' => $_GET['id']));
$f = $query->fetch();
$name = htmlspecialchars($f['name']);
$user = isset($_SESSION['songs/user']) ? $_SESSION['songs/user'] : "";
$title = $f ? $name : $trans['recording not found'];
?>
<?php include 'header.php'; ?>
<?php if (!$f) {
  http_response_code(404);
?>
<h1><?=$trans['recording not found']?></h1>
<p><?=$trans['recording not found reason']?></p>
<?php } else { ?>
<h1 class='user-text'><?= $name ?></h1>
<div class="author">
  <img class="myicon" src="<?=BASE_PATH?>/image/user.png">
  <span class="username"><?= $f['user'] ?></span>
  <span class="space"></span>
  <img class="myicon" src="<?=BASE_PATH?>/image/time.png">
  <span class="time"><?= $f['date'] ?></span>
</div>
<?php if ($user === $f['user']) { ?>
<a onclick="return confirm(Translation['really want to delete song?']+Translation['cannot be undone'])"
  href="deleteSong.php?id=<?=$_GET['id']?>"><?=$trans['delete']?></a>
<a href="editSong.php?id=<?=$_GET['id']?>"><?=$trans['edit']?></a>
<?php } ?>
<hr>
<p class='user-text'><?= htmlspecialchars($f['description']) ?></p>
<audio controls>
  <source src="<?=BASE_PATH?>/files/<?= $f['file'] ?>.ogg">
  <source src="<?=BASE_PATH?>/files/<?= $f['file'] ?>.m4a">
  <source src="<?=BASE_PATH?>/files/<?= $f['file'] ?>.wav" type="audio/wav">
  <source src="<?=BASE_PATH?>/files/<?= $f['file'] ?>.mp3" type="audio/mpeg">
  <!-- sorry IE users -->
  <embed src="<?=BASE_PATH?>/files/<?= $f['file'] ?>.mp3" width='250' height='40'
    autostart="False">
</audio>
<!--[if gte IE 8]><!-->
<h2><?=$trans['comment']?></h2>
<div id="lstComment">
</div>
  <?php if (isset($_SESSION['songs/user'])) { ?>

<script src="<?=BASE_PATH?>/js/validate.js" charset="utf-8"></script>
<form id='cmt' action="makeComment.php" method="post" onsubmit="return checkNoEmptyComment(event)">
  <input name='song' type="hidden" value='<?= $_GET['id'] ?>'>
  <textarea name='comment' maxlength="1000" placeholder="<?=$trans['enter your comment']?>"></textarea><br>
  <button type="submit" name='ok'><?=$trans['ok']?></button>
</form>
  <?php } else { ?>

<p><?=$trans['you must login to make comment']?></p>
  <?php } ?>
  
<!--<![endif]-->
<!--[if IE]>
<p><?=$trans['you are using old and unsupported ie']?></p>
<![endif]-->
<script src="<?=BASE_PATH?>/js/asyncComment.js" charset="utf-8"></script>
<?php } ?>
<hr>
<?php require 'footer.php';
