<?php
namespace Tests;

use PHPUnit\Framework\TestCase;
use PDO;
use PDOStatement;
use App\Controllers\EsercizioController;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Factory\ResponseFactory;

class EsercizioControllerTest extends TestCase {

    public function testQuery10RestituisceJsonPaginatoSenzaDb() {
        // 1. Mock Request
        $request = (new ServerRequestFactory())->createServerRequest('GET', '/10')
            ->withQueryParams(['min_fornitori' => 2, 'page' => 2, 'limit' => 5]);
        $response = (new ResponseFactory())->createResponse();

        // 2. Mock PDOStatement
        $mockStatement = $this->createMock(PDOStatement::class);
        $mockStatement->method('execute')->willReturn(true);
        $mockStatement->method('fetchAll')->willReturn([
            ['pid' => 'P1'],
            ['pid' => 'P2']
        ]);

        // 3. Mock PDO
        $mockPdo = $this->createMock(PDO::class);
        $mockPdo->method('prepare')->willReturn($mockStatement);

        // 4. Istanza Controller con DB finto
        $controller = new EsercizioController($mockPdo);

        // 5. Esecuzione
        $result = $controller->eseguiQuery10($request, $response);

        // 6. Asserzioni
        $this->assertEquals(200, $result->getStatusCode());

        $body = json_decode((string)$result->getBody(), true);

        $this->assertEquals('success', $body['status']);
        $this->assertCount(2, $body['data']);
        $this->assertEquals('P1', $body['data'][0]['pid']);
        $this->assertEquals(2, $body['meta']['page']);
        $this->assertEquals(5, $body['meta']['limit']);
    }
}