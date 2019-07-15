<?php
$no_translation = true;
session_start();
unset($_SESSION['songs/user']);
header("Location: index.php");
exit();
