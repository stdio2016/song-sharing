<?php
require_once('b.php');
http_response_code(404);
$title=$trans['page not found'];
?>
<?php include 'header.php' ?>
<header><h1><?=$trans['page not found']?></h1></header>
<hr>
<section>
<p><?=$trans['this is not the place you should come']?></p>
<p><?=$trans['you can ']?><a href='<?=BASE_PATH.'/'.$lang?>/index.php'><?=$trans['go to homepage']?></a></p>
</section>
<hr>
<?php include 'footer.php';
