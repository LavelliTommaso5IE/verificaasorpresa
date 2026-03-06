<?php
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
    if ($request->getMethod() === 'POST') {
        $data = (array)$request->getParsedBody();
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';

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
    if (empty($_SESSION['logged_in'])) return $response->withHeader('Location', '/login')->withStatus(302);

    $message = null;
    $status = null;
    $baseUrl = 'http://localhost:8080/admin'; 

    $callApi = function($method, $endpoint, $data = []) use ($baseUrl) {
        $options = [
            'http' => [
                'ignore_errors' => true,
                'method' => $method,
                'header' => "Content-Type: application/json\r\n",
            ]
        ];
        if (!empty($data)) {
            $options['http']['content'] = json_encode($data);
        }
        $res = @file_get_contents($baseUrl . $endpoint, false, stream_context_create($options));
        return $res ? json_decode($res, true) : ['status' => 'error', 'message' => 'Errore API.'];
    };

    if ($request->getMethod() === 'POST') {
        $parsedBody = (array)$request->getParsedBody();
        $action = $parsedBody['action'] ?? '';
        $resData = [];

        if ($action === 'add_pezzo') $resData = $callApi('POST', '/pezzi', ['pid' => $parsedBody['pid'], 'pnome' => $parsedBody['pnome'], 'colore' => $parsedBody['colore']]);
        elseif ($action === 'edit_pezzo') $resData = $callApi('PUT', '/pezzi/' . $parsedBody['pid'], ['pnome' => $parsedBody['pnome'], 'colore' => $parsedBody['colore']]);
        elseif ($action === 'delete_pezzo') $resData = $callApi('DELETE', '/pezzi/' . $parsedBody['pid']);
        
        elseif ($action === 'add_fornitore') $resData = $callApi('POST', '/fornitori', ['fid' => $parsedBody['fid'], 'fnome' => $parsedBody['fnome'], 'indirizzo' => $parsedBody['indirizzo']]);
        elseif ($action === 'edit_fornitore') $resData = $callApi('PUT', '/fornitori/' . $parsedBody['fid'], ['fnome' => $parsedBody['fnome'], 'indirizzo' => $parsedBody['indirizzo']]);
        elseif ($action === 'delete_fornitore') $resData = $callApi('DELETE', '/fornitori/' . $parsedBody['fid']);
        
        elseif ($action === 'add_catalogo') $resData = $callApi('POST', '/catalogo', ['fid' => $parsedBody['fid'], 'pid' => $parsedBody['pid'], 'costo' => (float)$parsedBody['costo']]);
        elseif ($action === 'edit_catalogo') $resData = $callApi('PUT', '/catalogo/' . $parsedBody['fid'] . '/' . $parsedBody['pid'], ['costo' => (float)$parsedBody['costo']]);
        elseif ($action === 'delete_catalogo') $resData = $callApi('DELETE', '/catalogo/' . $parsedBody['fid'] . '/' . $parsedBody['pid']);

        $message = $resData['message'] ?? "Azione non riconosciuta";
        $status = $resData['status'] ?? 'error';
    }

    // --- RECUPERO DATI PER I DROPDOWN ---
    $pezziResponse = $callApi('GET', '/pezzi?limit=1000');
    $fornitoriResponse = $callApi('GET', '/fornitori?limit=1000');
    $catalogoResponse = $callApi('GET', '/catalogo?limit=1000'); 
    
    $listaPezzi = $pezziResponse['data'] ?? [];
    $listaFornitori = $fornitoriResponse['data'] ?? [];
    
    // Appiattiamo l'array del catalogo per facilitare il filtraggio in JavaScript
    $rawCatalogo = $catalogoResponse['data'] ?? [];
    $listaCatalogo = [];
    foreach ($rawCatalogo as $row) {
        $flatRow = [];
        foreach ($row as $entity => $cols) {
            foreach ($cols as $key => $val) {
                $flatRow[$key] = $val;
            }
        }
        $listaCatalogo[] = $flatRow;
    }

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
    if (empty($_SESSION['logged_in'])) return $response->withHeader('Location', '/login')->withStatus(302);

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

    if (!array_key_exists($id, $queryInfo)) return $response->withStatus(404);

    $baseUrl = 'http://localhost:8080';
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