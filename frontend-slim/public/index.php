<?php
// Avvia le sessioni per l'autenticazione
session_start();

use Slim\Factory\AppFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

// ==========================================
// ROTTE DI AUTENTICAZIONE
// ==========================================
$app->map(['GET', 'POST'], '/login', function (Request $request, Response $response) {
    $error = null;
    
    // Se l'utente tenta il login
    if ($request->getMethod() === 'POST') {
        $data = (array)$request->getParsedBody();
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';

        // Credenziali fittizie (in attesa che l'API le gestisca dal DB)
        if ($email === 'admin@admin.com' && $password === 'admin123') {
            $_SESSION['logged_in'] = true;
            $_SESSION['user_email'] = $email;
            return $response->withHeader('Location', '/1')->withStatus(302);
        } else {
            $error = "Email o password errati. (Usa: admin@admin.com / admin123)";
        }
    }

    ob_start();
    require __DIR__ . '/../src/views/login.php';
    $html = ob_get_clean();

    $response->getBody()->write($html);
    return $response->withHeader('Content-Type', 'text/html');
});

$app->get('/logout', function (Request $request, Response $response) {
    session_destroy();
    return $response->withHeader('Location', '/login')->withStatus(302);
});

// ==========================================
// ROTTA PANNELLO DI AMMINISTRAZIONE
// ==========================================
$app->map(['GET', 'POST'], '/admin/gestione', function (Request $request, Response $response) {
    // Controllo Autenticazione
    if (empty($_SESSION['logged_in'])) {
        return $response->withHeader('Location', '/login')->withStatus(302);
    }

    $message = null;
    $status = null;

    if ($request->getMethod() === 'POST') {
        $parsedBody = (array)$request->getParsedBody();
        $action = $parsedBody['action'] ?? '';
        $baseUrl = 'https://dev.eu-01.alpinenode.it/SLIM_API/public/admin';

        $options = ['http' => ['ignore_errors' => true]];

        // GESTIONE CATALOGO
        if ($action === 'add') {
            $data = ['fid' => (int)$parsedBody['fid'], 'pid' => (int)$parsedBody['pid'], 'costo' => (float)$parsedBody['costo']];
            $options['http']['method'] = 'POST';
            $options['http']['header'] = "Content-Type: application/json\r\n";
            $options['http']['content'] = json_encode($data);
            $apiResp = @file_get_contents($baseUrl . '/catalogo', false, stream_context_create($options));
            
        } elseif ($action === 'delete') {
            $options['http']['method'] = 'DELETE';
            $apiResp = @file_get_contents($baseUrl . '/catalogo/' . (int)$parsedBody['fid'] . '/' . (int)$parsedBody['pid'], false, stream_context_create($options));
        } 
        // GESTIONE NUOVI FORNITORI
        elseif ($action === 'add_fornitore') {
            $data = ['fid' => (int)$parsedBody['fid'], 'fnome' => $parsedBody['fnome'], 'indirizzo' => $parsedBody['indirizzo'] ?? ''];
            $options['http']['method'] = 'POST';
            $options['http']['header'] = "Content-Type: application/json\r\n";
            $options['http']['content'] = json_encode($data);
            $apiResp = @file_get_contents($baseUrl . '/fornitori', false, stream_context_create($options));

        } elseif ($action === 'delete_fornitore') {
            $options['http']['method'] = 'DELETE';
            $apiResp = @file_get_contents($baseUrl . '/fornitori/' . (int)$parsedBody['fid'], false, stream_context_create($options));
        }

        if (isset($apiResp)) {
            $resData = json_decode($apiResp, true);
            $message = $resData['message'] ?? "Errore sconosciuto";
            $status = $resData['status'] ?? 'error';
        }
    }

    // Passiamo le info per mantenere la sidebar
    $queryInfo = [
        1 => ['title' => '1. Pezzi distinti'], 2 => ['title' => '2. Fornitori (tutti i pezzi)'],
        3 => ['title' => '3. Fornitori (tutti i pezzi x colore)'], 4 => ['title' => '4. Pezzi esclusivi'],
        5 => ['title' => '5. Fornitori costosi'], 6 => ['title' => '6. Prezzo più alto in assoluto'],
        7 => ['title' => '7. Fornitori esclusivi x colore'], 8 => ['title' => '8. Fornitori bicolore (AND)'],
        9 => ['title' => '9. Fornitori bicolore (OR)'], 10 => ['title' => '10. Pezzi multi-fornitore']
    ];

    ob_start();
    require __DIR__ . '/../src/views/admin.php';
    $html = ob_get_clean();

    $response->getBody()->write($html);
    return $response->withHeader('Content-Type', 'text/html');
});

// ==========================================
// ROTTE FRONTEND DATI (Explorer)
// ==========================================
$app->get('/', function (Request $request, Response $response) {
    if (empty($_SESSION['logged_in'])) return $response->withHeader('Location', '/login')->withStatus(302);
    return $response->withHeader('Location', '/1')->withStatus(302);
});

$app->get('/{id}', function (Request $request, Response $response, $args) {
    // Controllo Autenticazione
    if (empty($_SESSION['logged_in'])) {
        return $response->withHeader('Location', '/login')->withStatus(302);
    }

    $id = (int)$args['id'];
    
    $queryInfo = [
        1 => ['title' => '1. Pezzi distinti', 'path' => '/catalogo/pezzi', 'params' => []],
        2 => ['title' => '2. Fornitori (tutti i pezzi)', 'path' => '/fornitori/copertura-totale', 'params' => []],
        3 => ['title' => '3. Fornitori (tutti i pezzi x colore)', 'path' => '/fornitori/copertura-colore', 'params' => ['colore']],
        4 => ['title' => '4. Pezzi esclusivi', 'path' => '/pezzi/esclusiva-azienda', 'params' => ['azienda']],
        5 => ['title' => '5. Fornitori costosi', 'path' => '/catalogo/prezzo-sopra-media', 'params' => []],
        6 => ['title' => '6. Prezzo più alto in assoluto', 'path' => '/catalogo/prezzo-massimo', 'params' => []],
        7 => ['title' => '7. Fornitori esclusivi x colore', 'path' => '/fornitori/esclusiva-colore', 'params' => ['colore']],
        8 => ['title' => '8. Fornitori bicolore (AND)', 'path' => '/fornitori/doppio-colore', 'params' => ['colore1', 'colore2']],
        9 => ['title' => '9. Fornitori bicolore (OR)', 'path' => '/fornitori/almeno-un-colore', 'params' => ['colore1', 'colore2']],
        10 => ['title' => '10. Pezzi multi-fornitore', 'path' => '/pezzi/multi-fornitore', 'params' => ['min_fornitori']]
    ];

    if (!array_key_exists($id, $queryInfo)) {
        return $response->withStatus(404);
    }

    $baseUrl = 'https://dev.eu-01.alpinenode.it/SLIM_API/public';
    $apiUrl = $baseUrl . $queryInfo[$id]['path'];

    $queryParams = $request->getQueryParams();
    $apiParams = $queryParams;
    $actualLimit = (int)($queryParams['limit'] ?? 10);
    $apiParams['limit'] = $actualLimit + 1; 

    if (!empty($apiParams)) $apiUrl .= '?' . http_build_query($apiParams);

    $apiResponse = @file_get_contents($apiUrl);
    $result = $apiResponse ? json_decode($apiResponse, true) : null;

    $rawData = $result['data'] ?? [];
    $meta = $result['meta'] ?? [];
    $meta['limit'] = $actualLimit;

    $dati = [];
    foreach ($rawData as $row) {
        $flatRow = [];
        foreach ($row as $key => $val) {
            if (is_array($val)) foreach ($val as $subKey => $subVal) $flatRow[$subKey] = $subVal;
            else $flatRow[$key] = $val;
        }
        $dati[] = $flatRow;
    }

    $hasNextPage = false;
    if (count($dati) > $actualLimit) {
        $hasNextPage = true; 
        array_pop($dati);    
    }

    ob_start();
    require __DIR__ . '/../src/views/home.php';
    $html = ob_get_clean();

    $response->getBody()->write($html);
    return $response->withHeader('Content-Type', 'text/html');
});

$app->run();