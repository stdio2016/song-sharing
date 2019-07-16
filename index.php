<?php
$mypath = 'index.php';
require_once 'b.php';
$sql = "SELECT id, name, `date` FROM sounds";
$query = $db->prepare($sql);
$query->execute();
$names = $query->fetchAll();
$title = $trans['record list'];
?>
<?php include 'header.php'; ?>
<h1><?=$title?></h1>
<?php if (logged_in()) { ?>
<a href='upload.php'>Add sound</a>
<?php } ?>
<hr>
<?php if (!$names) { ?>
<p><?=$trans['constructing...']?></p>
<?php } else { ?>
<ul>
<?php foreach ($names as $f) { ?>
  <li>
    <p>
      <a href='play.php?id=<?=$f['id']?>'><span><?= htmlspecialchars($f['name']) ?></span></a>
      <br>
      <span><?=$trans['time:']?><?=$f['date']?></span>
    </p>
  </li>
<?php } ?>
</ul>
<!--[if IE]>
<p><?=$trans['you are using old and unsupported ie']?></p>
<![endif]-->
<?php } ?>
<hr>
<?php require 'footer.php';
