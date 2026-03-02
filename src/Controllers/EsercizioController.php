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

            $payload = [
                'status' => 'success',
                'data' => $formattedData,
                'meta' => [
                    'page' => $page,
                    'limit' => $limit,
                    'count' => count($formattedData)
                ]
            ];

            return $this->respond($response, $payload);
        } catch (\PDOException $e) {
            return $this->respond($response, [
                'status' => 'error',
                'message' => 'Errore del database: ' . $e->getMessage()
            ], 500);
        }
    }

    // =========================================================================
    // QUERY 1-10
    // =========================================================================

    public function eseguiQuery1(Request $request, Response $response) {
        $sql = "SELECT DISTINCT p.* FROM Pezzi p INNER JOIN Catalogo c ON p.pid = c.pid";
        return $this->executeSql($request, $response, $sql);
    }

    public function eseguiQuery2(Request $request, Response $response) {
        $sql = "SELECT f.* FROM Fornitori f WHERE NOT EXISTS (
                    SELECT p.pid FROM Pezzi p WHERE NOT EXISTS (
                        SELECT c.pid FROM Catalogo c WHERE c.fid = f.fid AND c.pid = p.pid
                    )
                )";
        return $this->executeSql($request, $response, $sql);
    }

    public function eseguiQuery3(Request $request, Response $response) {
        $colore = $request->getQueryParams()['colore'] ?? 'rosso';
        $sql = "SELECT f.* FROM Fornitori f WHERE NOT EXISTS (
                    SELECT p.pid FROM Pezzi p WHERE p.colore = :colore AND NOT EXISTS (
                        SELECT c.pid FROM Catalogo c WHERE c.fid = f.fid AND c.pid = p.pid
                    )
                )";
        return $this->executeSql($request, $response, $sql, ['colore' => $colore]);
    }

    public function eseguiQuery4(Request $request, Response $response) {
        $azienda = $request->getQueryParams()['azienda'] ?? 'Acme';
        $sql = "SELECT DISTINCT p.* FROM Pezzi p 
                JOIN Catalogo c ON p.pid = c.pid 
                JOIN Fornitori f ON c.fid = f.fid 
                WHERE f.fnome = :azienda AND p.pid NOT IN (
                    SELECT c2.pid FROM Catalogo c2 JOIN Fornitori f2 ON c2.fid = f2.fid WHERE f2.fnome != :azienda
                )";
        return $this->executeSql($request, $response, $sql, ['azienda' => $azienda]);
    }

    public function eseguiQuery5(Request $request, Response $response) {
        $sql = "SELECT c.*, p.pnome, p.colore, f.fnome FROM Catalogo c 
                JOIN Fornitori f ON c.fid = f.fid 
                JOIN Pezzi p ON c.pid = p.pid 
                WHERE c.costo > (SELECT AVG(c2.costo) FROM Catalogo c2 WHERE c2.pid = c.pid)";
        return $this->executeSql($request, $response, $sql);
    }

    public function eseguiQuery6(Request $request, Response $response) {
        $sql = "SELECT p.*, f.*, c.costo FROM Pezzi p 
                JOIN Catalogo c ON p.pid = c.pid 
                JOIN Fornitori f ON c.fid = f.fid 
                WHERE c.costo = (SELECT MAX(c2.costo) FROM Catalogo c2 WHERE c2.pid = p.pid)";
        return $this->executeSql($request, $response, $sql);
    }

    public function eseguiQuery7(Request $request, Response $response) {
        $colore = $request->getQueryParams()['colore'] ?? 'rosso';
        $sql = "SELECT DISTINCT f.* FROM Fornitori f 
                JOIN Catalogo c ON f.fid = c.fid JOIN Pezzi p ON c.pid = p.pid 
                WHERE p.colore = :colore AND c.fid NOT IN (
                    SELECT c2.fid FROM Catalogo c2 JOIN Pezzi p2 ON c2.pid = p2.pid WHERE p2.colore != :colore
                )";
        return $this->executeSql($request, $response, $sql, ['colore' => $colore]);
    }

    public function eseguiQuery8(Request $request, Response $response) {
        $c1 = $request->getQueryParams()['colore1'] ?? 'rosso';
        $c2 = $request->getQueryParams()['colore2'] ?? 'verde';
        $sql = "SELECT DISTINCT f.* FROM Fornitori f 
                JOIN Catalogo c1 ON f.fid = c1.fid JOIN Pezzi p1 ON c1.pid = p1.pid 
                JOIN Catalogo c2 ON f.fid = c2.fid JOIN Pezzi p2 ON c2.pid = p2.pid 
                WHERE p1.colore = :c1 AND p2.colore = :c2";
        return $this->executeSql($request, $response, $sql, ['c1' => $c1, 'c2' => $c2]);
    }

    public function eseguiQuery9(Request $request, Response $response) {
        $c1 = $request->getQueryParams()['colore1'] ?? 'rosso';
        $c2 = $request->getQueryParams()['colore2'] ?? 'verde';
        $sql = "SELECT DISTINCT f.* FROM Fornitori f 
                JOIN Catalogo c ON f.fid = c.fid JOIN Pezzi p ON c.pid = p.pid 
                WHERE p.colore IN (:c1, :c2)";
        return $this->executeSql($request, $response, $sql, ['c1' => $c1, 'c2' => $c2]);
    }

    public function eseguiQuery10(Request $request, Response $response) {
        $min = $request->getQueryParams()['min_fornitori'] ?? 2;
        $sql = "SELECT p.* FROM Pezzi p 
                JOIN Catalogo c ON p.pid = c.pid 
                GROUP BY p.pid HAVING COUNT(DISTINCT c.fid) >= :min";
        return $this->executeSql($request, $response, $sql, ['min' => $min]);
    }

    // =========================================================================
    // API FORNITORI E AMMINISTRATORI (CRUD Catalogo)
    // =========================================================================

    public function fornitoreAggiungiPezzoCatalogo(Request $request, Response $response) {
        $parsedBody = $request->getParsedBody();
        $user_fid = $request->getAttribute('user_fid');

        $pid = $parsedBody['pid'] ?? null;
        $costo = $parsedBody['costo'] ?? null;

        if (!$pid || !$costo) {
            return $this->respond($response, ['status' => 'error', 'message' => 'Dati mancanti'], 400);
        }

        try {
            $sql = "INSERT INTO Catalogo (fid, pid, costo) VALUES (:fid, :pid, :costo)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['fid' => $user_fid, 'pid' => $pid, 'costo' => $costo]);
            return $this->respond($response, ['status' => 'success', 'message' => 'Pezzo aggiunto al catalogo'], 201);
        } catch (\PDOException $e) {
            return $this->respond($response, ['status' => 'error', 'message' => 'Errore inserimento: ' . $e->getMessage()], 500);
        }
    }

    public function fornitoreModificaPezzoCatalogo(Request $request, Response $response, array $args) {
        $parsedBody = $request->getParsedBody();
        $user_fid = $request->getAttribute('user_fid');
        $pid = $args['pid'];
        $nuovo_costo = $parsedBody['costo'] ?? null;

        if ($nuovo_costo === null) {
            return $this->respond($response, ['status' => 'error', 'message' => 'Costo mancante'], 400);
        }

        $sql = "UPDATE Catalogo SET costo = :costo WHERE fid = :fid AND pid = :pid";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['costo' => $nuovo_costo, 'fid' => $user_fid, 'pid' => $pid]);

        if ($stmt->rowCount() === 0) {
            return $this->respond($response, ['status' => 'error', 'message' => 'Nessun pezzo aggiornato. Verifica i permessi o i dati.'], 404);
        }
        return $this->respond($response, ['status' => 'success', 'message' => 'Catalogo aggiornato']);
    }

    public function fornitoreRimuoviPezzoCatalogo(Request $request, Response $response, array $args) {
        $user_fid = $request->getAttribute('user_fid');
        $pid = $args['pid'];

        $sql = "DELETE FROM Catalogo WHERE fid = :fid AND pid = :pid";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['fid' => $user_fid, 'pid' => $pid]);
        return $this->respond($response, ['status' => 'success', 'message' => 'Pezzo rimosso dal catalogo']);
    }

    public function adminAggiungiPezzoCatalogo(Request $request, Response $response) {
        $parsedBody = $request->getParsedBody();
        $fid = $parsedBody['fid'] ?? null;
        $pid = $parsedBody['pid'] ?? null;
        $costo = $parsedBody['costo'] ?? null;

        if (!$fid || !$pid || !$costo) {
            return $this->respond($response, ['status' => 'error', 'message' => 'Dati mancanti'], 400);
        }

        try {
            $sql = "INSERT INTO Catalogo (fid, pid, costo) VALUES (:fid, :pid, :costo)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['fid' => $fid, 'pid' => $pid, 'costo' => $costo]);
            return $this->respond($response, ['status' => 'success', 'message' => 'Pezzo aggiunto al catalogo del fornitore'], 201);
        } catch (\PDOException $e) {
            return $this->respond($response, ['status' => 'error', 'message' => 'Errore inserimento: ' . $e->getMessage()], 500);
        }
    }

    public function adminRimuoviPezzoCatalogo(Request $request, Response $response, array $args) {
        $fid = $args['fid'];
        $pid = $args['pid'];

        $sql = "DELETE FROM Catalogo WHERE fid = :fid AND pid = :pid";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['fid' => $fid, 'pid' => $pid]);
        return $this->respond($response, ['status' => 'success', 'message' => 'Record rimosso globalmente']);
    }
    // =========================================================================
    // API AMMINISTRATORI (CRUD Fornitori)
    // =========================================================================

    // CREATE: Aggiunge un nuovo fornitore
    public function adminCreaFornitore(Request $request, Response $response) {
        $parsedBody = $request->getParsedBody();

        $fid = $parsedBody['fid'] ?? null;
        $fnome = $parsedBody['fnome'] ?? null;
        $indirizzo = $parsedBody['indirizzo'] ?? null;

        // fid e fnome sono obbligatori in base alla struttura del DB
        if (!$fid || !$fnome) {
            return $this->respond($response, ['status' => 'error', 'message' => 'Dati mancanti: fid e fnome sono obbligatori'], 400);
        }

        try {
            $sql = "INSERT INTO Fornitori (fid, fnome, indirizzo) VALUES (:fid, :fnome, :indirizzo)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'fid' => $fid,
                'fnome' => $fnome,
                'indirizzo' => $indirizzo
            ]);

            return $this->respond($response, ['status' => 'success', 'message' => 'Fornitore creato con successo'], 201);
        } catch (\PDOException $e) {
            // Se l'ID esiste già, il database lancerà un errore che catturiamo qui
            return $this->respond($response, ['status' => 'error', 'message' => 'Errore creazione: ' . $e->getMessage()], 500);
        }
    }

    // READ: Legge tutti i fornitori (utilizziamo executeSql per avere l'impaginazione automatica)
    public function adminLeggiFornitori(Request $request, Response $response) {
        $sql = "SELECT * FROM Fornitori";
        return $this->executeSql($request, $response, $sql);
    }

    // UPDATE: Modifica il nome o l'indirizzo di un fornitore esistente
    public function adminModificaFornitore(Request $request, Response $response, array $args) {
        $fid = $args['fid']; // Preso dall'URL
        $parsedBody = $request->getParsedBody();

        $fnome = $parsedBody['fnome'] ?? null;
        $indirizzo = $parsedBody['indirizzo'] ?? null;

        if (!$fnome) {
            return $this->respond($response, ['status' => 'error', 'message' => 'Il nome del fornitore (fnome) è obbligatorio per la modifica'], 400);
        }

        try {
            $sql = "UPDATE Fornitori SET fnome = :fnome, indirizzo = :indirizzo WHERE fid = :fid";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'fnome' => $fnome,
                'indirizzo' => $indirizzo,
                'fid' => $fid
            ]);

            if ($stmt->rowCount() === 0) {
                return $this->respond($response, ['status' => 'error', 'message' => 'Nessun fornitore aggiornato. Verifica che il fid sia corretto.'], 404);
            }

            return $this->respond($response, ['status' => 'success', 'message' => 'Fornitore aggiornato con successo']);
        } catch (\PDOException $e) {
            return $this->respond($response, ['status' => 'error', 'message' => 'Errore aggiornamento: ' . $e->getMessage()], 500);
        }
    }

    // DELETE: Elimina un fornitore (ATTENZIONE: elimina in automatico anche Catalogo e Utente collegati)
    public function adminEliminaFornitore(Request $request, Response $response, array $args) {
        $fid = $args['fid']; // Preso dall'URL

        try {
            $sql = "DELETE FROM Fornitori WHERE fid = :fid";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['fid' => $fid]);

            if ($stmt->rowCount() === 0) {
                return $this->respond($response, ['status' => 'error', 'message' => 'Fornitore non trovato.'], 404);
            }

            return $this->respond($response, ['status' => 'success', 'message' => 'Fornitore e tutti i suoi pezzi in catalogo eliminati con successo']);
        } catch (\PDOException $e) {
            return $this->respond($response, ['status' => 'error', 'message' => 'Errore eliminazione: ' . $e->getMessage()], 500);
        }
    }
}