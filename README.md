# 🛡️ Safe Notes - Web App Sicura con Login, OTP e Note Condivise

Safe Notes è un'applicazione PHP per la gestione di note condivise tra utenti, con autenticazione a due fattori (2FA), ruoli differenziati e controllo della visibilità delle note. Include funzionalità di registrazione, login sicuro, creazione ed eliminazione note in base al ruolo utente.

## 📦 Requisiti

- PHP >= 7.4
- Composer
- MySQL o MariaDB
- Server locale (es. XAMPP, ecc.)
- Libreria [RobThree/TwoFactorAuth](https://github.com/RobThree/TwoFactorAuth)

## 📁 Struttura dei file principali

```
/
├── index.php               # Pagina di login
├── register.php            # Pagina di registrazione
├── note.php                # Gestione delle note
├── logout.php              # Logout utente
├── verify_login.php        # Verifica OTP dopo login
├── verify_registration.php # Verifica OTP dopo registrazione
├── stile.css               # Stile CSS
├── db.php                  # Connessione al database
├── composer.json           # Dipendenze PHP
├── crypto.php              # Funzioni di crittografia
└── README.md               
```

## ⚙️ Installazione

1. **Clona o scarica il progetto nella cartella `htdocs` di XAMPP o simile:**

```bash
cd /percorso/della/htdocs
git clone https://github.com/Gnooodles/ProgettoCyber.git
```

2. **Posizionati nella cartella del progetto:**

```bash
cd ProgettoCyber
```

3. **Installa le dipendenze PHP con Composer: (Non necessario, solo in caso di problemi con il QR)**

```bash
composer require robthree/twofactorauth
```

4. **Crea il database MySQL:**

Apri **phpMyAdmin** o il tuo client MySQL ed esegui nella sezione SQL:

```sql
CREATE DATABASE IF NOT EXISTS safe_notes;

USE safe_notes;

CREATE TABLE utenti (
  email VARCHAR(255) PRIMARY KEY,
  password VARCHAR(255) NOT NULL,
  secret VARCHAR(255) NOT NULL,
  ruolo ENUM('amministratore', 'gruppo1', 'gruppo2', 'guest') NOT NULL
);

DROP TABLE IF EXISTS note;

CREATE TABLE note (
    id INT AUTO_INCREMENT PRIMARY KEY,
    autore_email VARCHAR(100) NOT NULL,
    gruppo_visibilita ENUM('amministratore', 'gruppo1', 'gruppo2', 'guest') NOT NULL,
    contenuto TEXT NOT NULL,
    data_creazione TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (autore_email) REFERENCES utenti(email)
);

```

5. **Aggiorna il file `db.php` con le credenziali del tuo database locale:**

```php
$host = 'localhost';
$utente = 'root';
$password = ''; // o la tua password se impostata
$nomeDB = 'safe_notes';
```

## 🚀 Come usare l'app

1. Avvia Apache e MySQL dal tuo pannello XAMPP.  
2. Crea i database e le tabelle come descritto sopra.  
3. Vai nel browser su `http://localhost/ProgettoCyber/`.  
4. Registrati  
   4.1 Ogni utente è registrato come guest  
   4.2 Il ruolo può essere cambiato da phpMyAdmin  
5. Scansiona il QR Code mostrato con Google Authenticator o simile.  
6. Inserisci il codice OTP per completare la registrazione.  
7. Effettua il login con email e password, poi inserisci l'OTP per accedere.  
8. Crea note visibili in base al tuo ruolo e gestiscile.

## 🔐 Ruoli e Permessi

| Ruolo          | Può vedere note di...                    | Può eliminare   |
|----------------|------------------------------------------|-----------------|
| amministratore | tutti                                    | tutte           |
| gruppo1        | gruppo1                                  | solo le proprie |
| gruppo2        | gruppo2                                  | solo le proprie |
| guest          | solo guest                               | solo le proprie |


## 📚 Librerie usate

- [RobThree/TwoFactorAuth](https://github.com/RobThree/TwoFactorAuth) - OTP TOTP generator

## 📝 Altre informazioni 

- La parte relativa alla sicurezza con https e virtual host è stata testata su un server locale con XAMPP, ma non è implementata in questi file.

## 🧑‍💻 Autori

Progetto sviluppato da Daniele Gnudi e Federico Battiato per l'esame di **Cybersecurity**.
