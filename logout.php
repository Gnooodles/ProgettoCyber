<?php

// Nascondi errori per la sicurezza
error_reporting(0);         
ini_set('display_errors', 0); // Disattiva la visualizzazione

session_start();
session_destroy();
setcookie(session_name(), '', time()-3600);  // Resetto i cookie di sessione
header("Location: index.php");
exit;
?>
