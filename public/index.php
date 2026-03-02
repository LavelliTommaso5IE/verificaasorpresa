<?php
require_once("../src/Core/config.php");
use DI\Container;
use Slim\Factory\AppFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require __DIR__ . '/../vendor/autoload.php';

// 1. Configurazione del Container
$container = new Container();

$container->set(PDO::class, function () {
    $host = DB_HOST;
    $dbname = DB_NAME;
    $user = DB_USER;
    $pass = DB_PASS;

    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $pdo;
});

// 2. Inizializzazione App
AppFactory::setContainer($container);
$app = AppFactory::create();
$app->setBasePath("/SLIM_API/public");

// 3. Middleware per forzare il JSON
$app->add(function (Request $request, $handler) {
    return $handler->handle($request)->withHeader('Content-Type', 'application/json');
});

$app->addErrorMiddleware(true, true, true);

// =========================================================================
// ROTTE DI LETTURA (Ex Query 1-10)
// =========================================================================

// Q1: Tutti i pezzi attualmente in catalogo (con info catalogo)
$app->get('/catalogo/pezzi', \App\Controllers\EsercizioController::class . ':eseguiQuery1');

// Q2: Fornitori che forniscono TUTTI i pezzi esistenti
$app->get('/fornitori/copertura-totale', \App\Controllers\EsercizioController::class . ':eseguiQuery2');

// Q3: Fornitori che forniscono TUTTI i pezzi di un colore specifico (?colore=rosso)
$app->get('/fornitori/copertura-colore', \App\Controllers\EsercizioController::class . ':eseguiQuery3');

// Q4: Pezzi forniti ESCLUSIVAMENTE da una specifica azienda (?azienda=Acme)
$app->get('/pezzi/esclusiva-azienda', \App\Controllers\EsercizioController::class . ':eseguiQuery4');

// Q5: Elementi del catalogo che costano più della media per quel pezzo
$app->get('/catalogo/prezzo-sopra-media', \App\Controllers\EsercizioController::class . ':eseguiQuery5');

// Q6: Pezzi venduti al loro prezzo massimo registrato
$app->get('/catalogo/prezzo-massimo', \App\Controllers\EsercizioController::class . ':eseguiQuery6');

// Q7: Fornitori che forniscono SOLO pezzi di un determinato colore (?colore=rosso)
$app->get('/fornitori/esclusiva-colore', \App\Controllers\EsercizioController::class . ':eseguiQuery7');

// Q8: Fornitori che forniscono pezzi di DUE colori specifici (?colore1=rosso&colore2=verde)
$app->get('/fornitori/doppio-colore', \App\Controllers\EsercizioController::class . ':eseguiQuery8');

// Q9: Fornitori che forniscono pezzi di ALMENO UNO di due colori (?colore1=rosso&colore2=verde)
$app->get('/fornitori/almeno-un-colore', \App\Controllers\EsercizioController::class . ':eseguiQuery9');

// Q10: Pezzi forniti da un numero minimo di fornitori (?min_fornitori=2)
$app->get('/pezzi/multi-fornitore', \App\Controllers\EsercizioController::class . ':eseguiQuery10');


// =========================================================================
// ROTTE FORNITORE (Richiederanno Autenticazione in futuro)
// =========================================================================

// CRUD sul proprio catalogo
$app->post('/fornitore/catalogo', \App\Controllers\EsercizioController::class . ':fornitoreAggiungiPezzoCatalogo');
$app->put('/fornitore/catalogo/{pid}', \App\Controllers\EsercizioController::class . ':fornitoreModificaPezzoCatalogo');
$app->delete('/fornitore/catalogo/{pid}', \App\Controllers\EsercizioController::class . ':fornitoreRimuoviPezzoCatalogo');


// =========================================================================
// ROTTE AMMINISTRATORE (Richiederanno Autenticazione Admin in futuro)
// =========================================================================

// Gestione globale del catalogo (nota i parametri nell'URL per la DELETE)
$app->post('/admin/catalogo', \App\Controllers\EsercizioController::class . ':adminAggiungiPezzoCatalogo');
$app->delete('/admin/catalogo/{fid}/{pid}', \App\Controllers\EsercizioController::class . ':adminRimuoviPezzoCatalogo');

// CRUD Fornitori (Amministratore)
$app->post('/admin/fornitori', \App\Controllers\EsercizioController::class . ':adminCreaFornitore');
$app->get('/admin/fornitori', \App\Controllers\EsercizioController::class . ':adminLeggiFornitori');
$app->put('/admin/fornitori/{fid}', \App\Controllers\EsercizioController::class . ':adminModificaFornitore');
$app->delete('/admin/fornitori/{fid}', \App\Controllers\EsercizioController::class . ':adminEliminaFornitore');

// 4. Avvio dell'applicazione
$app->run();