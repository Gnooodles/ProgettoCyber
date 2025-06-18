<?php
// Nascondi errori per la sicurezza
error_reporting(0);
ini_set('display_errors', 0);

// Connessione al database
$host = 'localhost';
$dbname = 'safe_notes'; 
$user = 'root';     
$pass = '';

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

// Variabile messaggio da mostrare nel form
$messaggio = "";

// Se il form è stato inviato
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Dati dal form (puliti e validati)
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validazione email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $messaggio = "Formato email non valido.";
    }
    // Numero caratteri password
    elseif (strlen($password) < 6) {
        $messaggio = "La password deve contenere almeno 6 caratteri.";
    }
    // Controllo password coincidano
    elseif ($password !== $confirm_password) {
        $messaggio = "Le password non coincidono.";
    }
    else {
        // Prevenzione XSS
        $email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');

        // Controllo se email già registrata
        $sql_check = "SELECT email FROM utenti WHERE email = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("s", $email);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            $messaggio = "Email già registrata.";
        } else {
            // Crittografia della password
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            $ruolo = "guest"; // ruolo predefinito

            // Inserimento utente
            $sql_insert = "INSERT INTO utenti (email, password, ruolo) VALUES (?, ?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param("sss", $email, $password_hash, $ruolo);

            if ($stmt_insert->execute()) {
                header("Location: index.php?registrazione=ok");
                exit(); 
            } else {
                $messaggio = "Errore nella registrazione. Riprova.";
            }

            $stmt_insert->close();
        }
        $stmt_check->close();
    }
}
$conn->close();
?>

<!-- HTML con form -->
<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8"> 
  <meta name="viewport" content="width=device-width, initial-scale=1"> 
  <title>SafeNotes - Registrazione</title> 
  <link rel="stylesheet" href="css/style.css"> 
</head>
<body>
  <div class="container"> 
    <h1>Safe Notes</h1> 

    <?php if (!empty($messaggio)): ?>
      <p style="color: red; text-align:center;"><?php echo $messaggio; ?></p>
    <?php endif; ?>

    <form action="register.php" method="post"> <!-- Form di registrazione che invia i dati a register.php -->

        <label for="email">Email</label> 
        <input type="email" name="email" required> <!-- Campo input per l'email -->

        <label for="password">Password</label> 
        <input type="password" name="password" required minlength="6"> <!-- Campo input per la password -->

        <label for="confirm_password">Conferma Password</label> 
        <input type="password" name="confirm_password" required> <!-- Campo input per confermare la password -->
        
        <button type="submit">Registrati</button> <!-- Bottone per inviare il form -->
        <p>Hai già un account? <a href="index.php">Login</a></p> 
    </form>
  </div>
</body>
</html>
