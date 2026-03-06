<?php
namespace App\Controllers;

use PDO;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class EsercizioController {

    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    private function respond(Response $response, array $payload, int $statusCode = 200): Response {
        $response->getBody()->write(json_encode($payload));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($statusCode);
    }

    private function formatData(array $data): array {
        $schema = [
            'pid' => 'pezzo', 'pnome' => 'pezzo', 'colore' => 'pezzo',
            'fid' => 'fornitore', 'fnome' => 'fornitore', 'indirizzo' => 'fornitore',
            'costo' => 'catalogo'
        ];

        $formattedData = [];

        foreach ($data as $row) {
            $formattedRow = [];
            foreach ($row as $key => $value) {
                $entity = $schema[$key] ?? 'extra';
                $formattedRow[$entity][$key] = $value;
            }
            if (count($formattedRow) === 1) {
                $formattedData[] = reset($formattedRow);
            } else {
                $formattedData[] = $formattedRow;
            }
        }

        return $formattedData;
    }

    private function executeSql(Request $request, Response $response, string $sql, array $params = []) {
        $queryParams = $request->getQueryParams();
        $page = isset($queryParams['page']) ? (int)$queryParams['page'] : 1;
        $limit = isset($queryParams['limit']) ? (int)$queryParams['limit'] : 10;
        $offset = ($page - 1) * $limit;

        $sql .= " LIMIT $limit OFFSET $offset";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $rawData = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $formattedData = $this->formatData($rawData);

            return $this->respond($response, [
                'status' => 'success',
                'data' => $formattedData,
                'meta' => ['page' => $page, 'limit' => $limit, 'count' => count($formattedData)]
            ]);
        } catch (\PDOException $e) {
            return $this->respond($response, ['status' => 'error', 'message' => 'Errore DB: ' . $e->getMessage()], 500);
        }
    }

    // =========================================================================
    // HELPERS PER CRUD (Riducono le ripetizioni di codice)
    // =========================================================================

    private function insertRecord(string $table, array $data): void {
        $keys = array_keys($data);
        $fields = implode(', ', $keys);
        $placeholders = ':' . implode(', :', $keys);
        $sql = "INSERT INTO $table ($fields) VALUES ($placeholders)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);
    }

    private function updateRecord(string $table, array $data, array $where): int {
        $setParts = [];
        $params = [];
        foreach ($data as $k => $v) {
            $setParts[] = "$k = :set_$k";
            $params["set_$k"] = $v;
        }
        $whereParts = [];
        foreach ($where as $k => $v) {
            $whereParts[] = "$k = :where_$k";
            $params["where_$k"] = $v;
        }

        $sql = "UPDATE $table SET " . implode(', ', $setParts) . " WHERE " . implode(' AND ', $whereParts);
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    private function deleteRecord(string $table, array $where): int {
        $whereParts = [];
        foreach ($where as $k => $v) $whereParts[] = "$k = :$k";
        $sql = "DELETE FROM $table WHERE " . implode(' AND ', $whereParts);
        $stmt = $this->db->prepare($sql);
        $stmt->execute($where);
        return $stmt->rowCount();
    }

    private function handleCrudAction(Response $response, callable $action, string $successMsg, int $successStatus = 200): Response {
        try {
            $affected = $action();
            if ($affected === 0) {
                return $this->respond($response, ['status' => 'error', 'message' => 'Nessun record trovato o modificato.'], 404);
            }
            return $this->respond($response, ['status' => 'success', 'message' => $successMsg], $successStatus);
        } catch (\PDOException $e) {
            return $this->respond($response, ['status' => 'error', 'message' => 'Errore DB: ' . $e->getMessage()], 500);
        }
    }

    // =========================================================================
    // QUERY 1-10 (Letture Complesse)
    // =========================================================================
    
    public function eseguiQuery1(Request $request, Response $response) {
        return $this->executeSql($request, $response, "SELECT DISTINCT p.* FROM Pezzi p INNER JOIN Catalogo c ON p.pid = c.pid");
    }

    public function eseguiQuery2(Request $request, Response $response) {
        $sql = "SELECT f.* FROM Fornitori f WHERE NOT EXISTS (SELECT p.pid FROM Pezzi p WHERE NOT EXISTS (SELECT c.pid FROM Catalogo c WHERE c.fid = f.fid AND c.pid = p.pid))";
        return $this->executeSql($request, $response, $sql);
    }

    public function eseguiQuery3(Request $request, Response $response) {
        $colore = $request->getQueryParams()['colore'] ?? 'rosso';
        $sql = "SELECT f.* FROM Fornitori f WHERE NOT EXISTS (SELECT p.pid FROM Pezzi p WHERE p.colore = :colore AND NOT EXISTS (SELECT c.pid FROM Catalogo c WHERE c.fid = f.fid AND c.pid = p.pid))";
        return $this->executeSql($request, $response, $sql, ['colore' => $colore]);
    }

    public function eseguiQuery4(Request $request, Response $response) {
        $azienda = $request->getQueryParams()['azienda'] ?? 'Acme';
        $sql = "SELECT DISTINCT p.* FROM Pezzi p JOIN Catalogo c ON p.pid = c.pid JOIN Fornitori f ON c.fid = f.fid WHERE f.fnome = :azienda AND p.pid NOT IN (SELECT c2.pid FROM Catalogo c2 JOIN Fornitori f2 ON c2.fid = f2.fid WHERE f2.fnome != :azienda)";
        return $this->executeSql($request, $response, $sql, ['azienda' => $azienda]);
    }

    public function eseguiQuery5(Request $request, Response $response) {
        $sql = "SELECT c.*, p.pnome, p.colore, f.fnome FROM Catalogo c JOIN Fornitori f ON c.fid = f.fid JOIN Pezzi p ON c.pid = p.pid WHERE c.costo > (SELECT AVG(c2.costo) FROM Catalogo c2 WHERE c2.pid = c.pid)";
        return $this->executeSql($request, $response, $sql);
    }

    public function eseguiQuery6(Request $request, Response $response) {
        $sql = "SELECT p.*, f.*, c.costo FROM Pezzi p JOIN Catalogo c ON p.pid = c.pid JOIN Fornitori f ON c.fid = f.fid WHERE c.costo = (SELECT MAX(c2.costo) FROM Catalogo c2 WHERE c2.pid = p.pid)";
        return $this->executeSql($request, $response, $sql);
    }

    public function eseguiQuery7(Request $request, Response $response) {
        $colore = $request->getQueryParams()['colore'] ?? 'rosso';
        $sql = "SELECT DISTINCT f.* FROM Fornitori f JOIN Catalogo c ON f.fid = c.fid JOIN Pezzi p ON c.pid = p.pid WHERE p.colore = :colore AND c.fid NOT IN (SELECT c2.fid FROM Catalogo c2 JOIN Pezzi p2 ON c2.pid = p2.pid WHERE p2.colore != :colore)";
        return $this->executeSql($request, $response, $sql, ['colore' => $colore]);
    }

    public function eseguiQuery8(Request $request, Response $response) {
        $c1 = $request->getQueryParams()['colore1'] ?? 'rosso';
        $c2 = $request->getQueryParams()['colore2'] ?? 'verde';
        $sql = "SELECT DISTINCT f.* FROM Fornitori f JOIN Catalogo c1 ON f.fid = c1.fid JOIN Pezzi p1 ON c1.pid = p1.pid JOIN Catalogo c2 ON f.fid = c2.fid JOIN Pezzi p2 ON c2.pid = p2.pid WHERE p1.colore = :c1 AND p2.colore = :c2";
        return $this->executeSql($request, $response, $sql, ['c1' => $c1, 'c2' => $c2]);
    }

    public function eseguiQuery9(Request $request, Response $response) {
        $c1 = $request->getQueryParams()['colore1'] ?? 'rosso';
        $c2 = $request->getQueryParams()['colore2'] ?? 'verde';
        $sql = "SELECT DISTINCT f.* FROM Fornitori f JOIN Catalogo c ON f.fid = c.fid JOIN Pezzi p ON c.pid = p.pid WHERE p.colore IN (:c1, :c2)";
        return $this->executeSql($request, $response, $sql, ['c1' => $c1, 'c2' => $c2]);
    }

    public function eseguiQuery10(Request $request, Response $response) {
        $min = $request->getQueryParams()['min_fornitori'] ?? 2;
        $sql = "SELECT p.* FROM Pezzi p JOIN Catalogo c ON p.pid = c.pid GROUP BY p.pid HAVING COUNT(DISTINCT c.fid) >= :min";
        return $this->executeSql($request, $response, $sql, ['min' => $min]);
    }

    // =========================================================================
    // API AMMINISTRATORI: CRUD PEZZI
    // =========================================================================

    public function adminLeggiPezzi(Request $request, Response $response) {
        return $this->executeSql($request, $response, "SELECT * FROM Pezzi");
    }

    public function adminCreaPezzo(Request $request, Response $response) {
        $data = $request->getParsedBody();
        if (empty($data['pid']) || empty($data['pnome'])) {
            return $this->respond($response, ['status' => 'error', 'message' => 'Dati mancanti: pid e pnome obbligatori'], 400);
        }
        
        // Validazione Regex per PID
        if (!preg_match('/^P[0-9]+$/', $data['pid'])) {
            return $this->respond($response, ['status' => 'error', 'message' => 'Il PID deve iniziare con P seguito da numeri (es. P1)'], 400);
        }

        return $this->handleCrudAction($response, function() use ($data) {
            $this->insertRecord('Pezzi', ['pid' => $data['pid'], 'pnome' => $data['pnome'], 'colore' => $data['colore'] ?? null]);
            return 1;
        }, 'Pezzo creato', 201);
    }

    public function adminModificaPezzo(Request $request, Response $response, array $args) {
        $data = $request->getParsedBody();
        $updateData = [];
        if (isset($data['pnome'])) $updateData['pnome'] = $data['pnome'];
        if (isset($data['colore'])) $updateData['colore'] = $data['colore'];

        if (empty($updateData)) return $this->respond($response, ['status' => 'error', 'message' => 'Nessun dato da aggiornare'], 400);

        return $this->handleCrudAction($response, fn() => $this->updateRecord('Pezzi', $updateData, ['pid' => $args['pid']]), 'Pezzo aggiornato');
    }

    public function adminEliminaPezzo(Request $request, Response $response, array $args) {
        return $this->handleCrudAction($response, fn() => $this->deleteRecord('Pezzi', ['pid' => $args['pid']]), 'Pezzo eliminato');
    }

    // =========================================================================
    // API AMMINISTRATORI: CRUD FORNITORI
    // =========================================================================

    public function adminLeggiFornitori(Request $request, Response $response) {
        return $this->executeSql($request, $response, "SELECT * FROM Fornitori");
    }

    public function adminCreaFornitore(Request $request, Response $response) {
        $data = $request->getParsedBody();
        if (empty($data['fid']) || empty($data['fnome'])) {
            return $this->respond($response, ['status' => 'error', 'message' => 'Dati mancanti: fid e fnome obbligatori'], 400);
        }

        // Validazione Regex per FID
        if (!preg_match('/^F[0-9]+$/', $data['fid'])) {
            return $this->respond($response, ['status' => 'error', 'message' => 'Il FID deve iniziare con F seguito da numeri (es. F1)'], 400);
        }

        return $this->handleCrudAction($response, function() use ($data) {
            $this->insertRecord('Fornitori', ['fid' => $data['fid'], 'fnome' => $data['fnome'], 'indirizzo' => $data['indirizzo'] ?? null]);
            return 1;
        }, 'Fornitore creato', 201);
    }

    public function adminModificaFornitore(Request $request, Response $response, array $args) {
        $data = $request->getParsedBody();
        $updateData = [];
        if (isset($data['fnome'])) $updateData['fnome'] = $data['fnome'];
        if (isset($data['indirizzo'])) $updateData['indirizzo'] = $data['indirizzo'];

        if (empty($updateData)) return $this->respond($response, ['status' => 'error', 'message' => 'Nessun dato da aggiornare'], 400);

        return $this->handleCrudAction($response, fn() => $this->updateRecord('Fornitori', $updateData, ['fid' => $args['fid']]), 'Fornitore aggiornato');
    }

    public function adminEliminaFornitore(Request $request, Response $response, array $args) {
        return $this->handleCrudAction($response, fn() => $this->deleteRecord('Fornitori', ['fid' => $args['fid']]), 'Fornitore eliminato');
    }

    // =========================================================================
    // API AMMINISTRATORI: CRUD CATALOGO GLOBALE
    // =========================================================================

    public function adminLeggiCatalogo(Request $request, Response $response) {
        return $this->executeSql($request, $response, "SELECT * FROM Catalogo");
    }

    public function adminAggiungiPezzoCatalogo(Request $request, Response $response) {
        $data = $request->getParsedBody();
        if (empty($data['fid']) || empty($data['pid']) || empty($data['costo'])) {
            return $this->respond($response, ['status' => 'error', 'message' => 'Dati mancanti: fid, pid e costo obbligatori'], 400);
        }
        return $this->handleCrudAction($response, function() use ($data) {
            $this->insertRecord('Catalogo', ['fid' => $data['fid'], 'pid' => $data['pid'], 'costo' => $data['costo']]);
            return 1;
        }, 'Pezzo aggiunto al catalogo globale', 201);
    }

    public function adminModificaPezzoCatalogo(Request $request, Response $response, array $args) {
        $data = $request->getParsedBody();
        if (!isset($data['costo'])) return $this->respond($response, ['status' => 'error', 'message' => 'Costo mancante'], 400);

        return $this->handleCrudAction($response, fn() => $this->updateRecord('Catalogo', ['costo' => $data['costo']], ['fid' => $args['fid'], 'pid' => $args['pid']]), 'Costo aggiornato nel catalogo globale');
    }

    public function adminRimuoviPezzoCatalogo(Request $request, Response $response, array $args) {
        return $this->handleCrudAction($response, fn() => $this->deleteRecord('Catalogo', ['fid' => $args['fid'], 'pid' => $args['pid']]), 'Record rimosso dal catalogo globale');
    }

    // =========================================================================
    // API FORNITORI (Azione limitata al proprio FID)
    // =========================================================================

    public function fornitoreAggiungiPezzoCatalogo(Request $request, Response $response) {
        $data = $request->getParsedBody();
        $user_fid = $request->getAttribute('user_fid');
        
        if (empty($data['pid']) || empty($data['costo'])) return $this->respond($response, ['status' => 'error', 'message' => 'Dati mancanti'], 400);

        return $this->handleCrudAction($response, function() use ($user_fid, $data) {
            $this->insertRecord('Catalogo', ['fid' => $user_fid, 'pid' => $data['pid'], 'costo' => $data['costo']]);
            return 1;
        }, 'Pezzo aggiunto al catalogo personale', 201);
    }

    public function fornitoreModificaPezzoCatalogo(Request $request, Response $response, array $args) {
        $data = $request->getParsedBody();
        $user_fid = $request->getAttribute('user_fid');

        if (!isset($data['costo'])) return $this->respond($response, ['status' => 'error', 'message' => 'Costo mancante'], 400);

        return $this->handleCrudAction($response, fn() => $this->updateRecord('Catalogo', ['costo' => $data['costo']], ['fid' => $user_fid, 'pid' => $args['pid']]), 'Costo aggiornato nel catalogo personale');
    }

    public function fornitoreRimuoviPezzoCatalogo(Request $request, Response $response, array $args) {
        $user_fid = $request->getAttribute('user_fid');
        return $this->handleCrudAction($response, fn() => $this->deleteRecord('Catalogo', ['fid' => $user_fid, 'pid' => $args['pid']]), 'Pezzo rimosso dal catalogo personale');
    }
}