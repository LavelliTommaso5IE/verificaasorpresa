<?php
require_once("../src/Core/config.php");
use DI\Container;
use Slim\Factory\AppFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require __DIR__ . '/../vendor/autoload.php';

// 1. Configurazione del Container per la Dependency Injection
$container = new Container();

// Diciamo al container come creare un oggetto PDO quando viene richiesto
$container->set(PDO::class, function () {
    $host = DB_HOST;
    $dbname = DB_NAME;
    $user = DB_USER;
    $pass = DB_PASS;

    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $pdo;
});

// 2. Inizializzazione dell'app con il Container
AppFactory::setContainer($container);
$app = AppFactory::create();
$app->setBasePath("/SLIM_API/public");

// 3. Middleware per forzare il JSON
$app->add(function (Request $request, $handler) {
    return $handler->handle($request)->withHeader('Content-Type', 'application/json');
});

$app->addErrorMiddleware(true, true, true);

// 4. Registrazione dinamica delle 10 rotte
for ($i = 1; $i <= 10; $i++) {
    $app->get("/{$i}", \App\Controllers\EsercizioController::class . ":eseguiQuery{$i}");
}

$app->run();