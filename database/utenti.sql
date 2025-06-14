CREATE DATABASE IF NOT EXISTS safe_notes;
USE safe_notes;

DROP TABLE IF EXISTS utenti;

CREATE TABLE utenti (
    email VARCHAR(100) PRIMARY KEY,
    password VARCHAR(255) NOT NULL,
    ruolo ENUM('amministratore', 'gruppo1', 'gruppo2', 'guest') NOT NULL DEFAULT 'guest'
);

INSERT INTO utenti (email, password, ruolo) VALUES
('admin@example.com', 'adminpass', 'amministratore'),
('gruppo1@example.com', 'password1', 'gruppo1'),
('gruppo2@example.com', 'password2', 'gruppo2'),
('guest@example.com', 'guestpass', 'guest');
