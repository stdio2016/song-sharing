<?php
$no_translation = true;
require_once 'b.php';
if (!isset($_SESSION['songs/user']) || !isset($_POST['song']) || !isset($_POST['comment'])) {
  echo 'fail';
  http_response_code(400);
  exit();
}
$sql = "INSERT INTO comment (user, song, comment, `time`, ip) VALUES (:user, :song, :comment, UTC_TIMESTAMP(), :ip)";
$query = $db->prepare($sql);
if ($_POST['comment']) {
  $query->execute(array(
    ':user' => $_SESSION['songs/user'],
    ':song' => $_POST['song'],
    ':comment' => $_POST['comment'],
    ':ip' => $_SERVER['REMOTE_ADDR']
  ));
}
$query->fetch();
echo 'success';
