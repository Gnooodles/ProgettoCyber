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
if (!isset($_SESSION['pending_email'], $_SESSION['pending_password_hash'], $_SESSION['pending_secret'], $_SESSION['pending_ruolo'])) {
    die("Sessione non valida. Torna alla <a href='register.php'>registrazione</a>.");
}

$email = $_SESSION['pending_email'];
$password_hash = $_SESSION['pending_password_hash'];
$secret = $_SESSION['pending_secret'];
$ruolo = $_SESSION['pending_ruolo'];


$messaggio = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otp = $_POST['otp_code'] ?? '';

    if ($tfa->verifyCode($secret, $otp)) {
        // OTP valido: inserisci utente in DB
        $stmt = $conn->prepare("INSERT INTO utenti (email, password, ruolo, secret) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $email, $password_hash, $ruolo, $secret);

        if ($stmt->execute()) {
            // Pulisci sessione temporanea
            unset($_SESSION['pending_email'], $_SESSION['pending_password_hash'], $_SESSION['pending_secret'], $_SESSION['pending_ruolo']);

            // Mostra pagina di successo
            ?>
            <!DOCTYPE html>
            <html lang="it">
            <head>
                <meta charset="UTF-8" />
                <title>Registrazione completata</title>
                <link rel="stylesheet" href="css/style.css" />
            </head>
            <body>
                <div class="container">
                    <h2>Registrazione completata con successo!</h2>
                    <p><a href="index.php">Vai al login</a></p>
                </div>
            </body>
            </html>
            <?php
            exit;
        }

        $stmt->close();
    } else {
        $messaggio = "Codice OTP errato. Riprova.";
    }
}

// Genera QR code per la verifica (mostra l'email come username)
$qr = $tfa->getQRCodeImageAsDataUri($email, $secret);
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Verifica Registrazione</title>
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

    <?php if (!empty($messaggio)): ?>
        <p style="color: red;"><?= htmlspecialchars($messaggio) ?></p>
    <?php endif; ?>

    <p>Scansiona il QR code con la tua app di autenticazione (Google Authenticator, Authy, ecc):</p>
    <img src="<?= $qr ?>" alt="QR Code" class="qr-code"><br><br>

    <form method="post" action="verify_registration.php">
        <label for="otp_code">Inserisci il codice OTP:</label><br>
        <input type="text" id="otp_code" name="otp_code" required autocomplete="off" pattern="\d{6}" title="Inserisci un codice OTP di 6 cifre"><br><br>
        <button type="submit">Conferma OTP</button>
    </form>
  </div>
</body>
</html>
