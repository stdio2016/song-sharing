<?php
$mypath = 'upload.php';
require_once 'b.php';
if (!logged_in()) {
  $_SESSION['songs/msg'] = $trans['you must login to upload sound'];
  header('Location: index.php');
  exit();
}
?>
<!DOCTYPE html>
<html lang="<?=$lang?>">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?=$trans['upload sound']?></title>
<link rel="stylesheet" href="<?=BASE_PATH?>/css/main.css">
</head>
<body>
<?php include 'login.php'; ?>
<h1><?=$trans['upload sound']?></h1>
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
<?php include 'c.php';
