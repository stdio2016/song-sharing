<?php
$no_translation = true;
require_once 'b.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  if (!isset($_GET['id'])) {
    echo 'bad request';
    http_response_code(400);
    exit;
  }
  header("Content-type: application/json");
  if (!isset($_GET['user'])) {
    $sql = "SELECT user,think FROM falsetto WHERE song=:song";
    $query = $db->prepare($sql);
    $query->execute(array(
      ':song' => $_GET['id']
    ));
    $f = $query->fetchAll();
    $first = True;
    echo "[\n";
    foreach ($f as $ff) {
      if (!$first) echo ',';
      echo '{"user":"';
      echo $ff['user'];
      echo '","marks":';
      echo $ff['think'];
      echo "}\n";
    }
    echo ']';
    exit;
  }
  $sql = "SELECT think FROM falsetto WHERE song=:song AND user=:user";
  $query = $db->prepare($sql);
  $query->execute(array(
    ':song' => $_GET['id'],
    ':user' => $_GET['user']
  ));
  $f = $query->fetch();
  if (!is_array($f)) {
    echo '"not found"';
    http_response_code(404);
  }
  else {
    echo $f['think'];
  }
}
else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  if (!logged_in()) {
    echo 'login required';
    http_response_code(403);
    exit;
  }
  if (!isset($_POST['id']) || !isset($_POST['marks'])) {
    echo 'bad request';
    http_response_code(400);
    exit;
  }
  $sql = "INSERT INTO falsetto(song,user,think) VALUES (:song,:user,:mark) ON DUPLICATE KEY UPDATE think=:mark";
  $query = $db->prepare($sql);
  $query->execute(array(
    ':song' => $_POST['id'],
    ':user' => $_SESSION['songs/user'],
    ':mark' => $_POST['marks']
  ));
  echo 'success';
}
else if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
  if (!logged_in()) {
    echo 'login required';
    http_response_code(403);
    exit;
  }
  if (!isset($_GET['id'])) {
    echo 'bad request';
    http_response_code(400);
    exit;
  }
  $sql = "DELETE FROM falsetto WHERE song=:song AND user=:user";
  $query = $db->prepare($sql);
  $query->execute(array(
    ':song' => $_GET['id'],
    ':user' => $_SESSION['songs/user']
  ));
  echo 'success';
}
else {
  echo 'method not supported';
  http_response_code(405);
}
