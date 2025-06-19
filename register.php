<?php
session_start();

// Nascondi errori per la sicurezza
error_reporting(0);         
ini_set('display_errors', 0); // Disattiva la visualizzazione

include 'db.php'; // File di connessione al database

require __DIR__ . '/vendor/autoload.php';
use RobThree\Auth\TwoFactorAuth;
use RobThree\Auth\Providers\Qr\QrServerProvider;

$tfa = new TwoFactorAuth(new QrServerProvider());


$messaggio = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $messaggio = "Formato email non valido.";
    }
    elseif (strlen($password) < 6) {
        $messaggio = "La password deve contenere almeno 6 caratteri.";
    }
    elseif ($password !== $confirm_password) {
        $messaggio = "Le password non coincidono.";
    }
    else {
        $email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');

        $sql_check = "SELECT email FROM utenti WHERE email = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("s", $email);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            $messaggio = "Email già registrata.";
        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $ruolo = "guest";

            // Genera secret OTP
            $secret = $tfa->createSecret();

            // Salva dati in sessione temporanea in attesa della verifica OTP
            $_SESSION['pending_email'] = $email;
            $_SESSION['pending_password_hash'] = $password_hash;
            $_SESSION['pending_secret'] = $secret;
            $_SESSION['pending_ruolo'] = $ruolo;

            // Reindirizza a pagina di verifica OTP
            header("Location: verify_registration.php");
            exit();
        }

        $stmt_check->close();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8"> 
  <title>SafeNotes - Registrazione</title> 
  <link rel="stylesheet" href="css/style.css"> 
</head>
<body>
  <!-- Gestione banner e impostazioni cookie -->
  <div id="cookie-banner" style="display: none;">
    Questo sito utilizza cookie tecnici per migliorare l’esperienza utente.
    <button onclick="acceptCookies()">Accetta</button>
  </div>

  <script>
    function acceptCookies() {
    localStorage.setItem('cookieAccepted', 'yes');
    document.getElementById('cookie-banner').style.display = 'none';
    }

    window.onload = function () {
      if (!localStorage.getItem('cookieAccepted')) {
        document.getElementById('cookie-banner').style.display = 'block';
      }
    };
  </script>

  <div class="container"> 
    <h1>Safe Notes</h1> 

    <?php if (!empty($messaggio)): ?>
      <p style="color: red; text-align:center;"><?php echo $messaggio; ?></p>
    <?php endif; ?>

    <form action="register.php" method="post">

        <label for="email">Email</label> 
        <input type="email" name="email" required> 

        <label for="password">Password</label> 
        <input type="password" name="password" required minlength="6"> 

        <label for="confirm_password">Conferma Password</label> 
        <input type="password" name="confirm_password" required> 
        
        <button type="submit">Registrati</button> 
        <p>Hai già un account? <a href="index.php">Login</a></p> 
    </form>
  </div>
</body>
</html>
