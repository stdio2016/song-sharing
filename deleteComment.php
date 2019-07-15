<?php
$no_translation = true;
require_once 'b.php';
if (!isset($_SESSION['songs/user']) || !isset($_GET['id'])) {
  header('Location: index.php');
  exit();
}
$sql = "SELECT user FROM comment WHERE id = :id";
$query = $db->prepare($sql);
$query->execute(array(':id' => $_GET['id']));
$f = $query->fetch();
if ($f['user'] != $_SESSION['songs/user']) {
  header('Location: index.php');
  exit();
}

$sql = "UPDATE comment SET deleted = 1 WHERE id = :id";
$query = $db->prepare($sql);
$query->execute(array(':id' => $_GET['id']));
header('Location: play.php?id=' . $_GET['song']);
exit();
