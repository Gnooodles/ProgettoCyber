<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Safe Notes - Le mie note</title>
  <link rel="stylesheet" href="css/style.css" />
</head>
<body>
  <div class="container">
    <h1>Le tue note</h1>

    <!-- Form per eliminare note selezionate -->
    <form id="eliminaNoteForm" method="POST" action="elimina_note.php">
      <div id="listaNote">
        <!--
          Qui verranno inserite dinamicamente le note dal backend PHP,
          esempio:
          <div>
            <input type="checkbox" name="note_ids[]" value="1" />
            <label>Contenuto della nota 1</label>
          </div>
        -->
      </div>

      <button type="submit">Elimina selezionate</button>
    </form>

    <hr />

    <!-- Form per aggiungere nuova nota -->
    <form id="aggiungiNotaForm" method="POST" action="aggiungi_nota.php">
      <label for="contenutoNota">Nuova nota</label>
      <textarea id="contenutoNota" name="contenuto" rows="3" required></textarea>
      <button type="submit">Aggiungi nota</button>
    </form>

  </div>
</body>
</html>
