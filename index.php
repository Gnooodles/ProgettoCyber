<?php
// Nascondi errori per la sicurezza
error_reporting(0);         
ini_set('display_errors', 0); // Disattiva la visualizzazione
?>

<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8"> 
  <meta name="viewport" content="width=device-width, initial-scale=1"> 
  <title>Safe Notes - Login</title> 
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
    <form action="login.php" method="post"> <!-- Form di login che invia i dati a login.php -->
      <label for="email">Email</label> 
      <input type="email" name="email" value="<?= htmlspecialchars($_COOKIE['user_email'] ?? '') ?>" required>  <!-- Campo input per l'email con cookie -->

      <label for="password">Password</label> 
      <input type="password" name="password" required> <!-- Campo input per la password -->

      <button type="submit">Login</button> 
      <p>Non hai un account? <a href="register.php">Registrati</a></p> <!-- Link per la registrazione -->
    </form>
  </div>
</body>
</html>