<?php
// for AJAX
$no_translation = true;
if (!isset($_GET['idafter']) || !isset($_GET['song'])) {
  header("Location: index.php");
  exit();
}
require_once 'b.php';
$sql = <<<HJ
  SELECT id, user, comment, time, deleted FROM comment
  WHERE song = :song AND id > :idafter
  ORDER BY id
HJ;
$query = $db->prepare($sql);
$query->execute(array(
  ':idafter' => $_GET['idafter'],
  ':song' => $_GET['song']
));
$comments = $query->fetchAll(PDO::FETCH_ASSOC);
foreach ($comments as &$c) {
  $c['deleted'] = (int)$c['deleted'];
  if ($c['deleted'] == 1) {
    unset($c['user']);
    unset($c['comment']);
    unset($c['time']);
  }
}
unset($c);
echo json_encode($comments, JSON_UNESCAPED_UNICODE);
