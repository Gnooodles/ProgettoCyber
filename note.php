<?php
session_start();

// Verifica se l'utente è loggato, se non lo è rimanda a index.php
if (!isset($_SESSION['email']) || !isset($_SESSION['ruolo'])) {
    header("Location: index.php");
    exit;
}

$email = $_SESSION['email'];
$ruolo = $_SESSION['ruolo'];

// Connessione al database
$host = 'localhost';
$dbname = 'safe_notes'; 
$user = 'root';     
$pass = '';

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

// Inserimento nuova nota
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nuova_nota'])) {
    $contenuto = trim($_POST['nuova_nota']);
    if ($contenuto !== '') {
        $sql = "INSERT INTO note (autore_email, gruppo_visibilita, contenuto) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $email, $ruolo, $contenuto);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: note.php"); // Previene il reinvio del form dopo refresh
    exit;
}

// Eliminazione note selezionate
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['elimina_note'])) {
    if (isset($_POST['note_ids']) && is_array($_POST['note_ids'])) {
        $ids = array_map('intval', $_POST['note_ids']); // pulizia base
        $id_list = implode(',', $ids);

        // Solo l'amministratore può eliminare qualsiasi nota
        if ($ruolo === 'amministratore') {
            $sql = "DELETE FROM note WHERE id IN ($id_list)";
        } else {
            // Gli altri utenti possono eliminare solo le loro note #TODO: Print che non è possibile eliminare note di altri utenti
            $sql = "DELETE FROM note WHERE id IN ($id_list) AND autore_email = '$email'";
        }

        $conn->query($sql);
    }
    header("Location: note.php");
    exit;
}

// Recupero note da mostrare
switch ($ruolo) {
    case 'amministratore':
        $sql = "SELECT * FROM note ORDER BY data_creazione DESC";
        break;
    case 'gruppo1':
    case 'gruppo2':
        $sql = "SELECT * FROM note WHERE gruppo_visibilita = '$ruolo' ORDER BY data_creazione DESC";
        break;
    case 'guest':
        $sql = "SELECT * FROM note WHERE gruppo_visibilita = 'guest' AND autore_email = '$email' ORDER BY data_creazione DESC";
        break;
}

$result = $conn->query($sql);
$note = $result->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Safe Notes - Le Tue Note</title>
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
        <h1>Benvenuto, <?= htmlspecialchars($email) ?> (<?= htmlspecialchars($ruolo) ?>)</h1>

        <!-- Form per inserire una nuova nota -->
        <form action="note.php" method="post">
            <label for="nuova_nota">Aggiungi nuova nota:</label>
            <textarea name="nuova_nota" rows="3" style="width:100%;" required></textarea>
            <button type="submit">Aggiungi nota</button>
        </form>

        <!-- Elenco note con checkbox per eliminare #TODO: Sistemare che le note sono in chiaro --> 
        <form action="note.php" method="post">
            <?php if (count($note) > 0): ?>
                <h2>Note</h2>
                <?php foreach ($note as $n): ?>
                    <div style="margin-bottom: 10px; border-bottom: 1px solid #ccc; padding-bottom: 5px;">
                        <input type="checkbox" name="note_ids[]" value="<?= $n['id'] ?>">
                        <strong><?= htmlspecialchars($n['contenuto']) ?></strong><br>
                        <small>Data: <?= $n['data_creazione'] ?> | Autore: <?= $n['autore_email'] ?> | Gruppo: <?= $n['gruppo_visibilita'] ?></small>
                    </div>
                <?php endforeach; ?>
                <button type="submit" name="elimina_note">Elimina selezionate</button>
            <?php else: ?>
                <p>Nessuna nota disponibile.</p>
            <?php endif; ?>
        </form>

        <!-- Logout -->
        <p style="margin-top: 20px;"><a href="logout.php">Logout</a></p>
    </div>
</body>
</html>
