<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SafeNotes - Login</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <div class="container">
    <h1>SafeNotes</h1>
    <form action="login.php" method="post">
      <label for="email">Email</label>
      <input type="email" name="email" required>

      <label for="password">Password</label>
      <input type="password" name="password" required>

      <button type="submit">Login</button>
      <p>Non hai un account? <a href="register.php">Registrati</a></p>
    </form>
  </div>
</body>
</html>
