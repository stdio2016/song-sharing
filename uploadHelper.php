<?php
require_once 'b.php';
if (!logged_in()) {
  $_SESSION['songs/msg'] = $trans['you must login to upload sound'];
  header('Location: index.php');
  exit();
}
if (!$_POST['name']) {
  $_SESSION['songs/msg'] = $trans['upload too big'];
  header('Location: index.php');
  exit();
}
$file = $_FILES['file'];
if ($file['error'] !== 0) {
  $_SESSION['songs/msg'] = $trans['upload too big'];
  header('Location: index.php');
  exit();
}
$file_type = $file['type'];
$mime = explode('/', $file_type)[0];
if ($mime !== 'audio' && $mime !== 'video') {
  $_SESSION['songs/msg'] = $trans['not audio file'];
  header('Location: index.php');
  exit();
}
$extension = strtolower(pathinfo(basename($file['name']), PATHINFO_EXTENSION));
if ($extension !== 'ogg' && $extension !== 'm4a' && $extension !== 'wav' && $extension !== 'mp3'
  && $extension !== 'flac') {
  $_SESSION['songs/msg'] = $trans['not audio file'];
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
if (move_uploaded_file($file['tmp_name'], "files/_$id.$extension")) {
  $_SESSION['songs/msg'] = $trans['upload success'];
  shell_exec("ffmpeg  -i files/_$id.$extension  -codec:a libmp3lame -vn -t 10:00 files/$id.mp3");
  shell_exec("ffmpeg  -i files/_$id.$extension  -codec:a libvorbis  -vn -t 10:00 files/$id.ogg");
  unlink("files/_$id.$extension");
  header('Location: index.php');
  exit();
}
$_SESSION['songs/msg'] = $trans['upload failed'].'(admin, please set permission of files folder)';
header('Location: index.php');
exit();
