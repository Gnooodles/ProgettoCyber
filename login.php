<?php
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
    die("Connessione al database fallita: " . $conn->connect_error);
}

// Recupera dati dal form
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

// Prepara e esegue la query per trovare l'utente
$sql = "SELECT * FROM utenti WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();

// Ottieni il risultato
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $utente = $result->fetch_assoc();

    // Controllo password #TODO: Sistemare che la password qui è in chiaro
    if ($password === $utente['password']) {
        // Login riuscito
        $_SESSION['email'] = $utente['email'];
        $_SESSION['ruolo'] = $utente['ruolo'];
        
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

<!-- Mostra errore in HTML (versione semplice) -->
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
