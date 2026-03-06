<?php
require_once("../src/Core/config.php");

use DI\Container;
use Slim\Factory\AppFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require __DIR__ . '/../vendor/autoload.php';

// =========================================================================
// 1. CONFIGURAZIONE DEL CONTAINER E DATABASE
// =========================================================================
$container = new Container();

$container->set(PDO::class, function () {
    $host = DB_HOST;
    $dbname = DB_NAME;
    $user = DB_USER;
    $pass = DB_PASS;

    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    // Imposta la modalità di errore di PDO su Exception per catturare facilmente gli errori SQL
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Disabilita l'emulazione delle prepare statements per maggiore sicurezza
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); 
    
    return $pdo;
});

// =========================================================================
// 2. INIZIALIZZAZIONE DELL'APPLICAZIONE
// =========================================================================
AppFactory::setContainer($container);
$app = AppFactory::create();

// =========================================================================
// 3. MIDDLEWARE
// =========================================================================

// FONDAMENTALE: Permette a Slim di parsare in automatico i JSON inviati nel body ($request->getParsedBody())
$app->addBodyParsingMiddleware(); 

// Middleware personalizzato per forzare la risposta in formato JSON per tutte le rotte
$app->add(function (Request $request, $handler) {
    $response = $handler->handle($request);
    return $response->withHeader('Content-Type', 'application/json');
});

// Middleware per la gestione degli errori (in produzione settare su false)
$app->addErrorMiddleware(true, true, true);


// =========================================================================
// 4. DEFINIZIONE DELLE ROTTE
// =========================================================================

use App\Controllers\EsercizioController;

// ---------------------------------------------------
// A. ROTTE DI LETTURA ESPLORATIVA (Query 1-10)
// ---------------------------------------------------
$app->get('/catalogo/pezzi',                EsercizioController::class . ':eseguiQuery1');
$app->get('/fornitori/copertura-totale',    EsercizioController::class . ':eseguiQuery2');
$app->get('/fornitori/copertura-colore',    EsercizioController::class . ':eseguiQuery3');
$app->get('/pezzi/esclusiva-azienda',       EsercizioController::class . ':eseguiQuery4');
$app->get('/catalogo/prezzo-sopra-media',   EsercizioController::class . ':eseguiQuery5');
$app->get('/catalogo/prezzo-massimo',       EsercizioController::class . ':eseguiQuery6');
$app->get('/fornitori/esclusiva-colore',    EsercizioController::class . ':eseguiQuery7');
$app->get('/fornitori/doppio-colore',       EsercizioController::class . ':eseguiQuery8');
$app->get('/fornitori/almeno-un-colore',    EsercizioController::class . ':eseguiQuery9');
$app->get('/pezzi/multi-fornitore',         EsercizioController::class . ':eseguiQuery10');


// ---------------------------------------------------
// B. ROTTE AMMINISTRATORE (Gestione Globale)
// ---------------------------------------------------

// CRUD Pezzi
$app->get('/admin/pezzi',                   EsercizioController::class . ':adminLeggiPezzi');
$app->post('/admin/pezzi',                  EsercizioController::class . ':adminCreaPezzo');
$app->put('/admin/pezzi/{pid}',             EsercizioController::class . ':adminModificaPezzo');
$app->delete('/admin/pezzi/{pid}',          EsercizioController::class . ':adminEliminaPezzo');

// CRUD Fornitori
$app->get('/admin/fornitori',               EsercizioController::class . ':adminLeggiFornitori');
$app->post('/admin/fornitori',              EsercizioController::class . ':adminCreaFornitore');
$app->put('/admin/fornitori/{fid}',         EsercizioController::class . ':adminModificaFornitore');
$app->delete('/admin/fornitori/{fid}',      EsercizioController::class . ':adminEliminaFornitore');

// CRUD Catalogo (Relazione Fornitore <-> Pezzo)
$app->get('/admin/catalogo',                EsercizioController::class . ':adminLeggiCatalogo');
$app->post('/admin/catalogo',               EsercizioController::class . ':adminAggiungiPezzoCatalogo');
$app->put('/admin/catalogo/{fid}/{pid}',    EsercizioController::class . ':adminModificaPezzoCatalogo');
$app->delete('/admin/catalogo/{fid}/{pid}', EsercizioController::class . ':adminRimuoviPezzoCatalogo');


// ---------------------------------------------------
// C. ROTTE FORNITORE (Gestione Catalogo Personale)
// ---------------------------------------------------
// Nota: queste rotte in futuro richiederanno un middleware di autenticazione 
// per estrarre l'ID del fornitore loggato ($request->getAttribute('user_fid')).

$app->post('/fornitore/catalogo',           EsercizioController::class . ':fornitoreAggiungiPezzoCatalogo');
$app->put('/fornitore/catalogo/{pid}',      EsercizioController::class . ':fornitoreModificaPezzoCatalogo');
$app->delete('/fornitore/catalogo/{pid}',   EsercizioController::class . ':fornitoreRimuoviPezzoCatalogo');


// =========================================================================
// 5. AVVIO DELL'APPLICAZIONE
// =========================================================================
$app->run();