<?php
$mypath = 'upload.php';
require_once 'b.php';
if (!logged_in()) {
  $_SESSION['songs/msg'] = $trans['you must login to upload sound'];
  header('Location: index.php');
  exit();
}
$title = $trans['upload sound'];
?>
<?php include 'header.php'; ?>
<h1><?=$title?></h1>
<hr>
<form id='formUpload' method="POST" action="uploadHelper.php" enctype="multipart/form-data">
<p>
  <label for="name"><?=$trans['name:']?></label>
  <input type="text" name="name" value="" required>
</p>
<p>
  <label for="file"><?=$trans['file:']?></label>
  <input type="file" name="file" accept=".ogg,.mp3,.wav,.flac,.m4a" oninput="tryListen()">
</p>
<p><?=$trans['audio restriction']?></p>
<p><audio id='tryAudio' controls></audio></p>
<p>
  <label for="description"><?=$trans['description:']?></label><br>
  <textarea name="description" rows="8"></textarea>
</p>
<button type="submit"><?=$trans['ok']?></button>
</form>
<script>
function tryListen() {
  URL.revokeObjectURL(tryAudio.src);
  tryAudio.src = URL.createObjectURL(formUpload.file.files[0]);
}
onunload = function () {
  URL.revokeObjectURL(tryAudio.src);
}
</script>
<hr>
<?php include 'footer.php';
