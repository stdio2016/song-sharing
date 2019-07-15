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
$file = $_FILES['file'];
if ($file['error'] !== 0) {
  $_SESSION['songs/msg'] = $trans['upload failed'];
  header('Location: index.php');
  exit();
}
$file_type = $file['type'];
if (explode('/', $file_type)[0] !== 'audio') {
  $_SESSION['songs/msg'] = '這不是聲音檔';
  header('Location: index.php');
  exit();
}
$extension = strtolower(pathinfo(basename($file['name']), PATHINFO_EXTENSION));
if ($extension !== 'ogg' && $extension !== 'm4a' && $extension !== 'wav' && $extension !== 'mp3') {
  $_SESSION['songs/msg'] = '這不是聲音檔';
  header('Location: index.php');
  exit();
}
$sql = "INSERT INTO sounds(name, `date`, file, description, user) VALUES(:name, now(), '', :description, :user)";
$query = $db->prepare($sql);
$query->execute(array(
  ':name' => $_POST['name'],
  ':description' => $_POST['description'],
  ':user' => $_SESSION['songs/user']
));
$id = $db->lastInsertId();
$sql = "UPDATE sounds SET file = :file where id = :id";
$query = $db->prepare($sql);
$query->execute(array(':file' => $id, ':id' => $id));
if (move_uploaded_file($file['tmp_name'], "files/$id.$extension")) {
  $_SESSION['songs/msg'] = $trans['upload success'];
  if ($extension !== 'mp3') {
    shell_exec("ffmpeg  -i files/$id.$extension  -codec:a libmp3lame -b:a 128k  files/$id.mp3");
  }
  if ($extension !== 'ogg') {
    shell_exec("ffmpeg  -i files/$id.$extension  -b:a 128k  files/$id.ogg");
  }
  header('Location: index.php');
  exit();
}
$_SESSION['songs/msg'] = $trans['upload failed'];
header('Location: index.php');
exit();
