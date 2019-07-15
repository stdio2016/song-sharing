<?php
session_start();
define('DB_TYPE', 'mysql');
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'songs');
define('DB_USER', 'songs');
define('DB_PASS', 'songs');

define('BASE_PATH', '/songs');

try {
  $db = array(PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ, PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING);
  $db = new PDO(DB_TYPE . ':host=' . DB_HOST . ';dbname=' . DB_NAME
    . ';charset=utf8mb4'
  , DB_USER, DB_PASS);
}
catch (Exception $x) {
  echo "<p>Failed to open database! What happened?</p>";
  echo $x;
  http_response_code(500);
  exit();
}

function getLang() {
  $supported_language = ['en', 'zh-tw', 'zh'];
  $langs = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
  $langs = array_map(function ($a) {
    $i = strpos($a, ";");
    if ($i !== false) {
      $a = substr($a, 0, $i);
    }
    return strtolower(trim($a));
  }, $langs);

  if (isset($_GET['lang'])) {
    $lang = $_GET['lang'];
  }
  else {
    $lang = 'en';
    foreach ($langs as $lang) {
      if (in_array($lang, $supported_language)) break;
    }
  }

  // to prevent bad guy from accessing strange file
  if (!in_array($lang, $supported_language)) {
    $lang = 'en';
  }
  return $lang;
}

function logged_in() {
  return isset($_SESSION['songs/user']);
}

if (!isset($no_translation)) {
  $lang = getLang();
  include("lang/en.php");
  include("lang/$lang.php");
}
