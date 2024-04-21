<?php
session_start();

// Odstrani vse sejne spremenljivke
$_SESSION = array();

// Uniči sejo
session_destroy();

// Preusmeri na stran za prijavo
header("Location: ../login/login.php");
exit();
?>
