<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8"> 
  <meta name="viewport" content="width=device-width, initial-scale=1"> 
  <title>Safe Notes - Login</title> 
  <link rel="stylesheet" href="css/style.css"> 
</head>
<body>
  <div class="container"> 
    <h1>Safe Notes</h1> 
    <form action="login.php" method="post"> <!-- Form di login che invia i dati a login.php -->
      <label for="email">Email</label> 
      <input type="email" name="email" required> <!-- Campo input per l'email -->

      <label for="password">Password</label> 
      <input type="password" name="password" required> <!-- Campo input per la password -->

      <button type="submit">Login</button> 
      <p>Non hai un account? <a href="register.php">Registrati</a></p> <!-- Link per la registrazione -->
    </form>
  </div>
</body>
</html>