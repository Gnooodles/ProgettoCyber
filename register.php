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
    <form action="register.php" method="post"> <!-- Form di registrazione che invia i dati a register.php -->

        <label for="email">Email</label> 
        <input type="email" name="email" required> <!-- Campo input per l'email -->

        <label for="password">Password</label> 
        <input type="password" name="password" required> <!-- Campo input per la password -->
    
        <label for="confirm_password">Conferma Password</label> 
        <input type="password" name="confirm_password" required> <!-- Campo input per confermare la password -->
        
        <button type="submit">Registrati</button> <!-- Bottone per inviare il form -->
        <p>Hai già un account? <a href="index.php">Login</a></p> 
    </form>
  </div>
</body>
</html>