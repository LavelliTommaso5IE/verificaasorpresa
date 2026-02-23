<?php
namespace App\Controllers;

use PDO;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class EsercizioController {

    private PDO $db;

    // Dependency Injection: riceve il DB dal Container (o dal Test)
    public function __construct(PDO $db) {
        $this->db = $db;
    }

    private function executeSql(Request $request, Response $response, string $sql, array $params = []) {
        $queryParams = $request->getQueryParams();

        $page = isset($queryParams['page']) ? (int)$queryParams['page'] : 1;
        $limit = isset($queryParams['limit']) ? (int)$queryParams['limit'] : 10;
        $offset = ($page - 1) * $limit;

        $sql .= " LIMIT $limit OFFSET $offset";

        // Usa l'istanza iniettata
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $payload = [
            'status' => 'success',
            'data' => $data,
            'meta' => ['page' => $page, 'limit' => $limit]
        ];

        $response->getBody()->write(json_encode($payload));
        return $response;
    }

    public function eseguiQuery1(Request $request, Response $response) {
        $sql = "SELECT DISTINCT p.pnome FROM Pezzi p INNER JOIN Catalogo c ON p.pid = c.pid";
        return $this->executeSql($request, $response, $sql);
    }

    public function eseguiQuery2(Request $request, Response $response) {
        $sql = "SELECT f.fnome FROM Fornitori f WHERE NOT EXISTS (
                    SELECT p.pid FROM Pezzi p WHERE NOT EXISTS (
                        SELECT c.pid FROM Catalogo c WHERE c.fid = f.fid AND c.pid = p.pid
                    )
                )";
        return $this->executeSql($request, $response, $sql);
    }

    public function eseguiQuery3(Request $request, Response $response) {
        $colore = $request->getQueryParams()['colore'] ?? 'rosso';
        $sql = "SELECT f.fnome FROM Fornitori f WHERE NOT EXISTS (
                    SELECT p.pid FROM Pezzi p WHERE p.colore = :colore AND NOT EXISTS (
                        SELECT c.pid FROM Catalogo c WHERE c.fid = f.fid AND c.pid = p.pid
                    )
                )";
        return $this->executeSql($request, $response, $sql, ['colore' => $colore]);
    }

    public function eseguiQuery4(Request $request, Response $response) {
        $azienda = $request->getQueryParams()['azienda'] ?? 'Acme';
        $sql = "SELECT p.pnome FROM Pezzi p JOIN Catalogo c ON p.pid = c.pid JOIN Fornitori f ON c.fid = f.fid 
                WHERE f.fnome = :azienda AND p.pid NOT IN (
                    SELECT c2.pid FROM Catalogo c2 JOIN Fornitori f2 ON c2.fid = f2.fid WHERE f2.fnome != :azienda
                )";
        return $this->executeSql($request, $response, $sql, ['azienda' => $azienda]);
    }

    public function eseguiQuery5(Request $request, Response $response) {
        $sql = "SELECT DISTINCT c.fid FROM Catalogo c 
                WHERE c.costo > (SELECT AVG(c2.costo) FROM Catalogo c2 WHERE c2.pid = c.pid)";
        return $this->executeSql($request, $response, $sql);
    }

    public function eseguiQuery6(Request $request, Response $response) {
        $sql = "SELECT p.pid, p.pnome, f.fnome FROM Pezzi p 
                JOIN Catalogo c ON p.pid = c.pid JOIN Fornitori f ON c.fid = f.fid 
                WHERE c.costo = (SELECT MAX(c2.costo) FROM Catalogo c2 WHERE c2.pid = p.pid)";
        return $this->executeSql($request, $response, $sql);
    }

    public function eseguiQuery7(Request $request, Response $response) {
        $colore = $request->getQueryParams()['colore'] ?? 'rosso';
        $sql = "SELECT DISTINCT c.fid FROM Catalogo c JOIN Pezzi p ON c.pid = p.pid 
                WHERE p.colore = :colore AND c.fid NOT IN (
                    SELECT c2.fid FROM Catalogo c2 JOIN Pezzi p2 ON c2.pid = p2.pid WHERE p2.colore != :colore
                )";
        return $this->executeSql($request, $response, $sql, ['colore' => $colore]);
    }

    public function eseguiQuery8(Request $request, Response $response) {
        $c1 = $request->getQueryParams()['colore1'] ?? 'rosso';
        $c2 = $request->getQueryParams()['colore2'] ?? 'verde';
        $sql = "SELECT c1.fid FROM Catalogo c1 JOIN Pezzi p1 ON c1.pid = p1.pid 
                JOIN Catalogo c2 ON c1.fid = c2.fid JOIN Pezzi p2 ON c2.pid = p2.pid 
                WHERE p1.colore = :c1 AND p2.colore = :c2";
        return $this->executeSql($request, $response, $sql, ['c1' => $c1, 'c2' => $c2]);
    }

    public function eseguiQuery9(Request $request, Response $response) {
        $c1 = $request->getQueryParams()['colore1'] ?? 'rosso';
        $c2 = $request->getQueryParams()['colore2'] ?? 'verde';
        $sql = "SELECT DISTINCT c.fid FROM Catalogo c JOIN Pezzi p ON c.pid = p.pid 
                WHERE p.colore IN (:c1, :c2)";
        return $this->executeSql($request, $response, $sql, ['c1' => $c1, 'c2' => $c2]);
    }

    public function eseguiQuery10(Request $request, Response $response) {
        $min = $request->getQueryParams()['min_fornitori'] ?? 2;
        $sql = "SELECT pid FROM Catalogo GROUP BY pid HAVING COUNT(DISTINCT fid) >= :min";
        return $this->executeSql($request, $response, $sql, ['min' => $min]);
    }
}