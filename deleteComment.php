<?php
$no_translation = true;
require_once 'b.php';
if ($_SERVER['REQUEST_METHOD'] != 'DELETE') {
  http_response_code(400);
  echo 'must use DELETE request';
  exit();
}
if (!isset($_SESSION['songs/user']) || !isset($_GET['id'])) {
  http_response_code(400);
  echo 'not enough args';
  exit();
}
$sql = "SELECT user FROM comment WHERE id = :id";
$query = $db->prepare($sql);
$query->execute(array(':id' => $_GET['id']));
$f = $query->fetch();
if ($f['user'] != $_SESSION['songs/user']) {
  http_response_code(403);
  echo 'permission denied';
  exit();
}

$sql = "UPDATE comment SET deleted = 1 WHERE id = :id";
$query = $db->prepare($sql);
$query->execute(array(':id' => $_GET['id']));
echo 'success';
exit();
