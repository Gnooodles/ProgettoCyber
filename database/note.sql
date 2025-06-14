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

-- Note di esempio

-- Note create dall'amministratore, visibili a tutti (perché l'amministratore può accedere ovunque)
INSERT INTO note (autore_email, gruppo_visibilita, contenuto)
VALUES 
('admin@example.com', 'amministratore', 'Verifica accessi utenti'),
('admin@example.com', 'amministratore', 'Scrivere report settimanale');

-- Note visibili solo a gruppo1
INSERT INTO note (autore_email, gruppo_visibilita, contenuto)
VALUES 
('gruppo1@example.com', 'gruppo1', 'Task: Progetto frontend'),
('gruppo1@example.com', 'gruppo1', 'Incontro con il team lunedì alle 10');

-- Note visibili solo a gruppo2
INSERT INTO note (autore_email, gruppo_visibilita, contenuto)
VALUES 
('gruppo2@example.com', 'gruppo2', 'Testare nuova API'),
('gruppo2@example.com', 'gruppo2', 'Scrivere documentazione modulo X');

-- Note personali del guest (visibili solo a lui stesso)
INSERT INTO note (autore_email, gruppo_visibilita, contenuto)
VALUES 
('guest@example.com', 'guest', 'Lista della spesa'),
('guest@example.com', 'guest', 'Appuntamento medico venerdì alle 15');
