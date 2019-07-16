<?php
require_once 'b.php';
if (isset($_POST['username'])) {
  if (!isset($_POST['back'])) {
    $_POST['back'] = 'index.php';
  }
  $test = preg_match("/^[A-Za-z0-9]{1,20}$/", $_POST['username']);
  if ($test != 1) {
    $_SESSION['songs/msg'] = $trans['username restriction'];
  }
  else {
    $_SESSION['songs/user'] = $_POST['username'];
  }
  header('Location:' . $_POST['back']);
  exit();
}
echo 'Bad request!!!';
http_response_code(400);
