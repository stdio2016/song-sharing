<?php
require_once 'b.php';
if (!isset($_SESSION['songs/user']) || !isset($_GET['id'])) {
  header('Location: index.php');
  exit();
}
$sql = "SELECT `user`, file FROM sounds WHERE id = :id";
$query = $db->prepare($sql);
$query->execute(array(':id' => $_GET['id']));
$f = $query->fetch();
if ($f['user'] != $_SESSION['songs/user']) {
  $_SESSION['songs/msg'] = "你不是檔案擁有者，不能刪除";
  header('Location: index.php');
  exit();
}

$possible_ext = ['m4a','ogg','wav','mp3'];
foreach ($possible_ext as $ext) {
  $filename = 'files/'.$f['file'].'.'.$ext;
  if (file_exists($filename)) {
    unlink($filename);
  }
}
$sql = "DELETE FROM sounds WHERE id = :id";
$query = $db->prepare($sql);
$query->execute(array(':id' => $_GET['id']));
$_SESSION['songs/msg'] = $trans['delete success'];
header('Location: index.php');
exit();
