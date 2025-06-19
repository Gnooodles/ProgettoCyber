<?php
session_start();

// Nascondi errori per la sicurezza
error_reporting(0);         
ini_set('display_errors', 0); // Disattiva la visualizzazione

include 'db.php'; // File di connessione al database

$messaggio = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $messaggio = "Email non valida.";
    } else {
        $email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
        $stmt = $conn->prepare("SELECT * FROM utenti WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $utente = $result->fetch_assoc();

            if (password_verify($password, $utente['password'])) {
                $_SESSION['otp_email'] = $utente['email'];
                $_SESSION['otp_ruolo'] = $utente['ruolo'];
                header("Location: verify_login.php");
                exit;
            } else {
                $messaggio = "Password errata.";
            }
        } else {
            $messaggio = "Utente non trovato.";
        }

        $stmt->close();
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title>Safe Notes</title>
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
        <p style="color:red;"><?= htmlspecialchars($messaggio) ?></p>
    <?php endif; ?>

    <form method="post" action="index.php">
      <label for="email">Email</label>
      <input type="email" name="email" value="<?= htmlspecialchars($_COOKIE['user_email'] ?? '') ?>" required>

      <label for="password">Password</label>
      <input type="password" name="password" required>

      <button type="submit">Login</button>
      <p>Non hai un account? <a href="register.php">Registrati</a></p>
    </form>
  </div>
</body>
</html>
