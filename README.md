# Verifica a Sorpresa - Slim Framework API

Questo repository contiene un'API RESTful sviluppata in **PHP** utilizzando **Slim Framework 4**. Il progetto risolve un classico esercizio di basi di dati (Fornitori, Pezzi, Catalogo) esponendo 10 endpoint specifici che restituiscono i risultati in formato JSON, completi di supporto per la **paginazione** e parametri dinamici.

## 🌐 Sito Demo Live

L'API è attualmente ospitata e testabile online senza necessità di configurazione locale. Puoi effettuare le richieste direttamente al seguente Base URL:

**URL Demo:** [https://dev.eu-01.alpinenode.it/SLIM_API/public/1] *(es. https://dev.eu-01.alpinenode.it/SLIM_API/public/1)*

## 🚀 Requisiti di Sistema

**PHP:** 7.4 o superiore (consigliato 8.x) con estensione pdo_mysql abilitata.
**Composer:** Per la gestione delle dipendenze.
**MySQL / MariaDB:** Per il database relazionale.

## 🛠️ Installazione

**1. Clona il repository**
bash
git clone [https://github.com/LavelliTommaso5IE/verificaasorpresa.git](https://github.com/LavelliTommaso5IE/verificaasorpresa.git)
cd verificaasorpresa



**2. Installa le dipendenze**
Il progetto utilizza Composer per gestire Slim Framework, PHP-DI (per la Dependency Injection) e PHPUnit.
bash
composer install



**3. Configura il Database**
Crea un database vuoto nel tuo server MySQL (es. fornitori_db).
Importa il file SQL fornito nella cartella database:
bash
mysql -u root -p fornitori_db < database/fornitori_db.sql



**4. Configura le Credenziali**
Apri il file src/Core/configExample.php, aggiorna le credenziali del database e rinomina in config.php:
php
$host = 'localhost';
$dbname = 'fornitori_db';
$user = 'tuo_utente';
$pass = 'tua_password';



## 💻 Avvio dell'Applicazione

Puoi utilizzare il server web integrato di PHP per testare rapidamente l'API in ambiente di sviluppo. Dalla cartella principale del progetto, esegui:

bash
php -S localhost:8080 -t public


L'API sarà ora in ascolto all'indirizzo http://localhost:8080.

## 📡 Endpoints Disponibili

L'API espone 10 endpoint, mappati da /1 a /10. Tutte le risposte hanno il Content-Type application/json.

### Paginazione
Tutti gli endpoint supportano la paginazione tramite parametri in Query String:
page: Numero della pagina (default: 1)
limit: Risultati per pagina (default: 10)
Esempio: GET /1?page=2&limit=5

### Parametri Dinamici
Alcune query accettano parametri personalizzati per filtrare i risultati:
**Query 3 e 7:** ?colore=rosso
**Query 4:** ?azienda=Acme
**Query 8 e 9:** ?colore1=rosso&colore2=verde
**Query 10:** ?min_fornitori=2

## 🧪 Test Unitari (PHPUnit)

Il progetto include test unitari che utilizzano i **Mock Objects** per simulare le connessioni al database tramite Dependency Injection. Questo garantisce che i test siano isolati, veloci e non modifichino il database reale.

Per eseguire l'intera suite di test, lancia:
bash
vendor/bin/phpunit


(In alternativa, puoi utilizzare lo script rapido: composer test)
