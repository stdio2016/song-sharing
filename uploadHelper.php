<?php
require_once 'b.php';
if (!logged_in()) {
  $_SESSION['songs/msg'] = $trans['you must login to upload sound'];
  header('Location: index.php');
  exit();
}
if (!isset($_FILES['file']) || !isset($_POST['name'])) {
  header('Location: index.php');
  exit();
}
var_dump($_FILES['file']);
$_SESSION['songs/msg'] = $trans['upload success'];
header('Location: index.php');
exit();
