<?php
$mypath = 'upload.php';
require_once 'b.php';
if (!logged_in()) {
  $_SESSION['songs/msg'] = $trans['you must login to upload sound'];
  header('Location: index.php');
  exit();
}
$title = $trans['upload sound'];
?>
<?php include 'header.php'; ?>
<h1><?=$title?></h1>
<hr>
<form method="POST" action="uploadHelper.php" enctype="multipart/form-data">
<p>
  <label for="name"><?=$trans['name:']?></label>
  <input type="text" name="name" value="">
</p>
<p>
  <label for="file"><?=$trans['file:']?></label>
  <input type="file" name="file">
</p>
<p>
  <label for="description"><?=$trans['description:']?></label><br>
  <textarea name="description" rows="8" style="width:100%;max-width:600px;"></textarea>
</p>
<button type="submit"><?=$trans['ok']?></button>
</form>
<hr>
<?php include 'footer.php';
