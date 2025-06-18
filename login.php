<?php

// Nascondi errori per la sicurezza
error_reporting(0);         
ini_set('display_errors', 0); // Disattiva la visualizzazione

session_start();

// Connessione al database
$host = 'localhost';
$dbname = 'safe_notes'; 
$user = 'root';     
$pass = '';        

// Crea connessione
$conn = new mysqli($host, $user, $pass, $dbname);

// Controlla la connessione
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

// Recupera dati dal form
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

// Controllo email valida
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Email non valida");
}

// Escaping per prevenire esecuzione codice html/javascript
$email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');

// Prepara e esegue la query per trovare l'utente
$sql = "SELECT * FROM utenti WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();

// Ottieni il risultato
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $utente = $result->fetch_assoc();

    // Controllo password precedentemente crittografata
    if (password_verify($password, $utente['password'])) {
        // Login riuscito
        $_SESSION['email'] = $utente['email'];
        $_SESSION['ruolo'] = $utente['ruolo'];

        // Salva l'email dell'utente in un cookie valido per 30 giorni
        setcookie("user_email", $email, time() + (86400 * 30), "/");

        // Reindirizzamento alla pagina delle note
        header("Location: note.php");
        exit;
    } else {
        $errore = "Password errata.";
    }
} else {
    $errore = "Utente non trovato.";
}

$conn->close();
?>

<!-- Mostra errore in HTML -->
<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title>Login fallito</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <div class="container">
    <h1>Login fallito</h1>
    <p><?= htmlspecialchars($errore) ?></p>
    <p><a href="index.php">Torna al login</a></p>
  </div>
</body>
</html>
