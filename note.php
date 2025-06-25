<?php

// Nascondi errori per la sicurezza
error_reporting(0);         
ini_set('display_errors', 0); // Disattiva la visualizzazione

session_start();

include 'db.php'; // File di connessione al database
include 'crypto.php'; // File con funzioni di cifratura

// Verifica se l'utente è loggato, se non lo è rimanda a index.php
if (!isset($_SESSION['email']) || !isset($_SESSION['ruolo'])) {
    header("Location: index.php");
    exit;
}

$email = $_SESSION['email'];
$ruolo = $_SESSION['ruolo'];

// Inserimento nuova nota (con cifratura)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nuova_nota'])) {
    $contenuto = trim($_POST['nuova_nota']);
    if ($contenuto !== '') {
        $contenuto_cifrato = encryptData($contenuto);
        $sql = "INSERT INTO note (autore_email, gruppo_visibilita, contenuto) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $email, $ruolo, $contenuto_cifrato);
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

        if (count($ids) > 0) {
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $types = str_repeat('i', count($ids));

            if ($ruolo === 'amministratore') {
                // Admin può eliminare tutte le note selezionate
                $stmt = $conn->prepare("DELETE FROM note WHERE id IN ($placeholders)");
                $stmt->bind_param($types, ...$ids);
                $stmt->execute();

            } else {
                // Utente normale: elimina solo note proprie
                $stmt = $conn->prepare("DELETE FROM note WHERE id IN ($placeholders) AND autore_email = ?");
                $params = array_merge($ids, [$email]);
                $stmt->bind_param($types . 's', ...$params);
                $stmt->execute();

                // Messaggio errore se alcune note non sono state eliminate
                if ($stmt->affected_rows < count($ids)) {
                    $_SESSION['messaggio_note'] = "Alcune note non sono state eliminate perché non ti appartengono.";
                }
            }
        }
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

    <?php if (!empty($_SESSION['messaggio_note'])): ?>
    <p style="color: red;"><?= htmlspecialchars($_SESSION['messaggio_note']) ?></p>
    <?php unset($_SESSION['messaggio_note']); ?>
    <?php endif; ?>

    <form action="note.php" method="post">
        <label for="nuova_nota">Aggiungi nuova nota:</label>
        <textarea name="nuova_nota" rows="3" style="width:100%;" required></textarea>
        <button type="submit">Aggiungi nota</button>
    </form>

    <form action="note.php" method="post">
        <?php if (count($note) > 0): ?>
            <h2>Note</h2>
            <?php foreach ($note as $n): ?>
                <?php
                    $contenuto_decifrato = decryptData($n['contenuto']);
                    if ($contenuto_decifrato === false) $contenuto_decifrato = '[Errore nella decifratura]';
                ?>
                <div style="margin-bottom: 10px; border-bottom: 1px solid #ccc; padding-bottom: 5px;">
                    <input type="checkbox" name="note_ids[]" value="<?= $n['id'] ?>">
                    <strong><?= htmlspecialchars($contenuto_decifrato) ?></strong><br>
                    <small>Data: <?= $n['data_creazione'] ?> | Autore: <?= $n['autore_email'] ?> | Gruppo: <?= $n['gruppo_visibilita'] ?></small>
                </div>
            <?php endforeach; ?>
            <button type="submit" name="elimina_note">Elimina selezionate</button>
        <?php else: ?>
            <p>Nessuna nota disponibile.</p>
        <?php endif; ?>
    </form>

    <p style="margin-top: 20px;"><a href="logout.php">Logout</a></p>
</div>
</body>
</html>
