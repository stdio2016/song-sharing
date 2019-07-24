<?php
$mypath = 'play.php?id=' . $_GET['id'];
require_once 'b.php';
$sql = "SELECT name, `date`, file, description, user FROM sounds where id = :id";
$query = $db->prepare($sql);
if (!isset($_GET['id'])) {
  header('Location: index.php');
  exit();
}
$query->execute(array(':id' => $_GET['id']));
$f = $query->fetch();
$name = htmlspecialchars($f['name']);
$user = isset($_SESSION['songs/user']) ? $_SESSION['songs/user'] : "";
$title = $f ? "Tag ".$name : $trans['recording not found'];
?>
<?php include 'header.php'; ?>
<?php if (!$f) {
  http_response_code(404);
?>
<h1><?=$trans['recording not found']?></h1>
<p><?=$trans['recording not found reason']?></p>
<?php } else { ?>
<h1 class='user-text'><?= $title ?></h1>
<hr>
<script>
var filename = <?=json_encode($f['file'])?>;
</script>
<?php if (!logged_in()) { ?>
<p>You must be logged in to mark voice</p>
<?php } ?>
<p class="visualizer">
  Waveform: <br>
  <canvas id="canvas" class="visualizer" height="60"></canvas>
</p>
<p class="visualizer">
  Marks: <br>
  <canvas id="canvasMark" class="marker" height="60"></canvas>
</p>
<p>
  <button type="button" onclick="zoomIn()">Zoom in</button>
  <button type="button" onclick="zoomOut()">Zoom out</button>
  <button type="button" onclick="selectVisible()">Select visible</button>
  <button type="button" onclick="playVisible()">Play</button>
  <button type="button" onclick="playSelected()">Play selected</button>
  <button type="button" onclick="stopSound()">Stop</button>
</p>
<form id='selReg'>
  <span>Mark as voice register: </span>
  <input name="selMark" type="radio" value="M" id="sel1"><label for="sel1">Modal voice</label>
  <input name="selMark" type="radio" value="I" id="sel2"><label for="sel2">Mixed voice</label>
  <input name="selMark" type="radio" value="H" id="sel3"><label for="sel3">Head voice</label>
  <input name="selMark" type="radio" value="F" id="sel4"><label for="sel4">Falsetto</label>
  <input name="selMark" type="radio" value="Y" id="sel5"><label for="sel5">Vocal fry</label>
  <input name="selMark" type="radio" value="-" id="sel6"><label for="sel6">Not voice</label>
  <input name="selMark" type="radio" value=" " id="sel7"><label for="sel7">Unknown</label>
  <br>
  <button type="button" onclick="markSelected()">Mark selection</button>
</form>
<script src="<?=BASE_PATH?>/js/waveform.js" charset="utf-8"></script>
<script src="<?=BASE_PATH?>/js/helpTag.js" charset="utf-8"></script>
<?php } ?>
<p>
<a href='<?=BASE_PATH.'/'.$lang?>/index.php'><?=$trans['go to homepage']?></a>
</p>
<hr>
<?php require 'footer.php';
