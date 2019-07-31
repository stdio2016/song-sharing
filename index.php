<?php
$mypath = 'index.php';
require_once 'b.php';
$sql = "SELECT id, name, `date`, user, views FROM sounds";
$query = $db->prepare($sql);
$query->execute();
$names = $query->fetchAll();
$title = $trans['record list'];
?>
<?php include 'header.php'; ?>
<h1><?=$title?></h1>
<?php if (logged_in()) { ?>
<a href='upload.php'><?=$trans['upload sound']?></a>
<?php } ?>
<hr>
<?php if (!$names) { ?>
<p><?=$trans['constructing...']?></p>
<?php } else { ?>
<ul>
<?php foreach ($names as $f) { ?>
  <li>
    <a href='play.php?id=<?=$f['id']?>' class='need-width user-text'><span><?= htmlspecialchars($f['name']) ?></span></a>
    <div class="author">
      <div class="field">
        <img class="myicon" src="<?=BASE_PATH?>/image/user.png">
        <span class="username"><?= $f['user'] ?></span>
      </div>
      <div class="field">
        <img class="myicon" src="<?=BASE_PATH?>/image/time.png">
        <span class="time"><?= $f['date'] ?></span>
      </div>
      <div class="field">
        <img class="myicon" src="<?=BASE_PATH?>/image/eye.png">
        <span class="views"><?= $f['views'] ?></span>
      </div>
    </div>
  </li>
<?php } ?>
</ul>
<!--[if IE]>
<p><?=$trans['you are using old and unsupported ie']?></p>
<![endif]-->
<?php } ?>
<hr>
<?php require 'footer.php';
