<?php
// Connessione al database
$host = 'localhost';
$dbname = 'safe_notes'; 
$user = 'root';     
$pass = '';

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}
?>