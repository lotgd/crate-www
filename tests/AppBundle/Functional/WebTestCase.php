<?php

namespace Tests\AppBundle\Functional;

use Doctrine\ORM\Tools\SchemaTool;
use Liip\FunctionalTestBundle\Test\WebTestCase as BaseWebTestCase;

class WebTestCase extends BaseWebTestCase
{
    private static $dbLoaded = false;

    public function testNothing() {
        $this->assertTrue(true);
    }

    public function __setUp()
    {
        if (self::$dbLoaded) {
            return;
        }
        $this->resetDatabase();
        self::$dbLoaded = true;
    }

    protected function __resetDatabase()
    {
    }

    protected function queryHelper($query, $jsonVariables)
    {
        $client = static::makeClient();
        $path = $this->getUrl('overblog_graphql_endpoint');

        $client->request(
            'GET', $path, ['query' => $query, 'variables' => $jsonVariables], [], ['CONTENT_TYPE' => 'application/graphql']
        );
        $result = $client->getResponse()->getContent();

        return [$result, $client];
    }

    protected function assertQuery($query, $jsonExpected, $jsonVariables = '{}')
    {
        list($result, $client) = $this->queryHelper($query, $jsonVariables);

        $this->assertStatusCode(200, $client);
        $this->assertEquals(json_decode($jsonExpected, true), json_decode($result, true), $result);
    }

    protected function assertArrayKeysInQuery($query, $subKey, $arrayKeys, $jsonVariables = "{}")
    {
        list($result, $client) = $this->queryHelper($query, $jsonVariables);

        $this->assertStatusCode(200, $client);

        $resultArray = json_decode($result, true);

        $this->assertNotNull($resultArray['data']);
        $this->assertArrayHasKey("data", $resultArray);
        $this->assertArrayHasKey($subKey, $resultArray["data"]);
        foreach ($arrayKeys as $key) {
            $this->assertArrayHasKey($key, $resultArray["data"][$subKey]);
        }
    }
}
