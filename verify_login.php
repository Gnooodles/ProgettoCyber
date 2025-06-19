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

// Controlla che ci siano i dati in sessione
if (!isset($_SESSION['otp_email'])) {
    die("Accesso non autorizzato. Fai prima il login.");
}

$email = $_SESSION['otp_email'];

// Recupera il secret dal DB
$stmt = $conn->prepare("SELECT secret, ruolo FROM utenti WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($secret, $ruolo);
if (!$stmt->fetch()) {
    die("Utente non trovato.");
}
$stmt->close();

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otp_code = $_POST['otp_code'] ?? '';

    if ($tfa->verifyCode($secret, $otp_code)) {
        // OTP corretto: setta la sessione definitiva e cancella quella temporanea
        $_SESSION['email'] = $email;
        $_SESSION['ruolo'] = $ruolo;
        $_SESSION['tfa_verified'] = true;

        unset($_SESSION['otp_email']);

        // Redirect alla pagina protetta
        header('Location: note.php');
        exit;
    } else {
        $error = "Codice OTP errato.";
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Verifica OTP</title>
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
        <h2>Verifica OTP</h2>
        <p>Inserisci il codice generato dalla tua app di autenticazione</p>
        <?php if (!empty($error)): ?>
            <p style="color:red;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="post" action="verify_login.php">
            <input type="text" name="otp_code" required autofocus><br><br>
            <button type="submit">Verifica OTP</button>
        </form>
    </div>
</body>
</html>
