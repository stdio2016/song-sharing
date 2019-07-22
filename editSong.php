<?php
require_once 'b.php';
if (!logged_in()) {
  $_SESSION['songs/msg'] = $trans['you must login to edit description'];
  header('Location: index.php');
  exit();
}
if (!isset($_REQUEST['id'])) {
  http_response_code(400);
  echo 'bad request';
  exit();
}
$mypath = 'editSong.php?id='.$_REQUEST['id'];
$sql = "SELECT user FROM sounds WHERE id = :id";
$query = $db->prepare($sql);
$query->execute(array(':id' => $_REQUEST['id']));
$f = $query->fetch();
if (!$f) {
  http_response_code(404);
  echo 'song not found';
  exit();
}
if ($f['user'] != $_SESSION['songs/user']) {
  http_response_code(403);
  echo 'permission denied';
  exit();
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $sql = "UPDATE sounds SET name=:name, description=:description WHERE id = :id";
  $query = $db->prepare($sql);
  $query->execute(array(
    ':id' => $_POST['id'],
    ':name' => $_POST['name'],
    ':description' => $_POST['description']
  ));
  
  $_SESSION['songs/msg'] = $trans['edit success'];
  header('Location: play.php?id='.$_POST['id']);
  exit();
}
$sql = "SELECT name,description FROM sounds WHERE id = :id";
$query = $db->prepare($sql);
$query->execute(array(':id' => $_REQUEST['id']));
$f = $query->fetch();
$title = $trans['edit sound'];
?>
<?php include 'header.php'; ?>
<h1><?=$title?></h1>
<hr>
<form id='formEdit' method="POST" action="editSong.php">
<input type=hidden name=id value=<?=$_GET['id']?>>
<p>
  <label for="name"><?=$trans['name:']?></label>
  <input type="text" name="name" value="<?=htmlspecialchars($f['name'])?>" required>
</p>
<p>
  <label for="description"><?=$trans['description:']?></label><br>
  <textarea name="description" rows="8"></textarea>
</p>
<script>
formEdit.description.value = <?=json_encode($f['description'])?>;
</script>
<button type="submit"><?=$trans['ok']?></button>
</form>
<hr>
<?php include 'footer.php';
