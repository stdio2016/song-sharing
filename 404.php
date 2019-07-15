<?php
require_once('b.php');
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="<?=$lang?>">
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta charset='utf8'>
  <title><?=$trans['page not found']?></title>
  <link rel="stylesheet" href="<?=BASE_PATH?>/css/main.css">
</head>
<body>
<?php include 'login.php' ?>
<header><h1><?=$trans['page not found']?></h1></header>
<hr>
<section>
<p><?=$trans['this is not the place you should come']?></p>
<p><?=$trans['you can ']?><a href='<?=BASE_PATH.'/'.$lang?>/index.php'><?=$trans['go to homepage']?></a></p>
</section>
<hr>
<?php include 'c.php';
