CREATE DATABASE IF NOT EXISTS safe_notes;
USE safe_notes;

DROP TABLE IF EXISTS utenti;

CREATE TABLE utenti (
    email VARCHAR(100) PRIMARY KEY,
    password VARCHAR(255) NOT NULL,
    secret VARCHAR(255) DEFAULT NULL,
    ruolo ENUM('amministratore', 'gruppo1', 'gruppo2', 'guest') NOT NULL DEFAULT 'guest'
);
