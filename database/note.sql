USE safe_notes;

DROP TABLE IF EXISTS note;

CREATE TABLE note (
    id INT AUTO_INCREMENT PRIMARY KEY,
    autore_email VARCHAR(100) NOT NULL,
    gruppo_visibilita ENUM('amministratore', 'gruppo1', 'gruppo2', 'guest') NOT NULL,
    contenuto TEXT NOT NULL,
    data_creazione TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (autore_email) REFERENCES utenti(email)
);
