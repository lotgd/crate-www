<?php

namespace LotGD\Crate\GraphQL\Tests\Functional\GraphQL;

use Doctrine\Common\Util\Debug;
use LotGD\Core\EventHandler;
use LotGD\Core\Events\EventContext;
use LotGD\Core\Game;
use LotGD\Crate\GraphQL\AppBundle\GraphQL\Types\CharacterStatIntType;
use LotGD\Crate\GraphQL\AppBundle\GraphQL\Types\CharacterStatRangeType;


class TestEventProvider implements EventHandler
{
    public static function handleEvent(Game $g, EventContext $context): EventContext
    {
        $stats = $context->getDataField("value");
        $character = $context->getDataField("character");

        $stats = array_merge($stats, [
            new CharacterStatIntType("lotgd/core/level", "Level", $character->getLevel()),
            new CharacterStatIntType("lotgd/core/attack", "Attack", $character->getAttack()),
            new CharacterStatIntType("lotgd/core/defense", "Defense", $character->getDefense()),
            new CharacterStatRangeType("lotgd/core/health", "Health", $character->getHealth(), $character->getMaxHealth()),
        ]);

        $context->setDataField("value", $stats);
        return $context;
    }
}

class CharacterQueryTest extends GraphQLTestCase
{
    public function testIfCharacterQueryWithoutArgumentsReturnsNull()
    {
        $query = <<<'EOF'
query CharacterQuery {
    character {
        id
        name
        displayName
        level
        attack
        defense
        health
        maxHealth
    }
}
EOF;

        $jsonExpected = <<<JSON
{
    "data": {
        "character": null
    }
}
JSON;

        $this->assertQuery($query, $jsonExpected);
    }

    public function testIfCharacterQueryWithIdReturnsCorrectCharacter()
    {
        $query = <<<'EOF'
query CharacterQuery($id: String) {
    character(characterId: $id) {
        id
        name
        displayName
        level
        attack
        defense
        health
        maxHealth
    }
}
EOF;

        $jsonVariables = <<<JSON
{
    "id": "10000000-0000-0000-0000-000000000001"
}
JSON;

        $jsonExpected = <<<JSON
{
    "data": {
        "character": {
            "id": "10000000-0000-0000-0000-000000000001",
            "name": "DB-Test",
            "displayName": "Novice DB-Test",
            "level": 1,
            "attack": 1,
            "defense": 1,
            "health": 10,
            "maxHealth": 10
        }
    }
}
JSON;

        $this->assertQuery($query, $jsonExpected, $jsonVariables);
    }

    public function testIfCharacterQueryWithNameReturnsCorrectCharacter()
    {
        $query = <<<'EOF'
query CharacterQuery($name: String) {
    character(characterName: $name) {
        id
        name
        displayName
    }
}
EOF;

        $jsonVariables = <<<JSON
{
    "name": "One"
}
JSON;

        $jsonExpected = <<<JSON
{
    "data": {
        "character": {
            "id": "10000000-0000-0000-0000-000000000002",
            "name": "One",
            "displayName": "The One And Only"
        }
    }
}
JSON;

        $results = $this->getQueryResults($query, $jsonVariables);
        $this->assertQuery($query, $jsonExpected, $jsonVariables);
    }

    public function testIfQueryOnStatsReturnPublicStatsIfUserNotAuthenticated()
    {
        $query = <<<'EOF'
query CharacterQuery($name: String) {
    character(characterName: $name) {
        id
        stats {
            id
            name
            type
            
            ... on CharacterStatInt {
                value
            }
            
            ... on CharacterStatRange {
                currentValue
                maxValue
            }
        }
    }
}
EOF;

        $jsonVariables = <<<JSON
{
    "name": "One"
}
JSON;

        $jsonExpected = <<<JSON
{
    "data": {
        "character": {
            "id": "10000000-0000-0000-0000-000000000002",
            "stats": [{
                "id":"lotgd\/core\/level",
                "name":"Level", 
                "type":"CharacterStatInt",
                "value":100
            }, {
                "id":"lotgd\/core\/attack",
                "name":"Attack",
                "type":"CharacterStatInt",
                "value":100
            }, {
                "id":"lotgd\/core\/defense",
                "name":"Defense",
                "type":"CharacterStatInt",
                "value":100
            },{
                "id":"lotgd\/core\/health",
                "name":"Health",
                "type":"CharacterStatRange",
                "currentValue":1000,
                "maxValue":1000
            }]
        }
    }
}
JSON;

        /** @var Game $game */
        $game = self::$game;
        $game->getEventManager()->subscribe("#h/lotgd/crate-graphql/characterStats/public#", TestEventProvider::class, "lotgd/test");
        $game->getEntityManager()->flush();

        $results = $this->getQueryResults($query, $jsonVariables);
        $this->assertQueryResult($jsonExpected, $results);

        $game->getEventManager()->unsubscribe("#h/lotgd/crate-graphql/characterStats/public#", TestEventProvider::class, "lotgd/test");
    }

    public function testIfQueryOnStatsNotReturnPrivateStatsIfUserNotAuthenticated()
    {
        $query = <<<'EOF'
query CharacterQuery($name: String) {
    character(characterName: $name) {
        id
        stats {
            id
            name
            type
            
            ... on CharacterStatInt {
                value
            }
            
            ... on CharacterStatRange {
                currentValue
                maxValue
            }
        }
    }
}
EOF;

        $jsonVariables = <<<JSON
{
    "name": "One"
}
JSON;

        $jsonExpected = <<<JSON
{
    "data": {
        "character": {
            "id": "10000000-0000-0000-0000-000000000002",
            "stats": []
        }
    }
}
JSON;

        /** @var Game $game */
        $game = self::$game;
        $game->getEventManager()->subscribe("#h/lotgd/crate-graphql/characterStats/private#", TestEventProvider::class, "lotgd/test");

        $results = $this->getQueryResults($query, $jsonVariables);
        $this->assertQueryResult($jsonExpected, $results);

        $game->getEventManager()->unsubscribe("#h/lotgd/crate-graphql/characterStats/private#", TestEventProvider::class, "lotgd/test");
    }

    public function testIfQueryOnStatsReturnPublicStatsIfUserAuthenticated()
    {
        $query = <<<'EOF'
query CharacterQuery($name: String) {
    character(characterName: $name) {
        id
        stats {
            id
            name
            type
            
            ... on CharacterStatInt {
                value
            }
            
            ... on CharacterStatRange {
                currentValue
                maxValue
            }
        }
    }
}
EOF;

        $jsonVariables = <<<JSON
{
    "name": "DB-Test"
}
JSON;

        $jsonExpected = <<<JSON
{
    "data": {
        "character": {
            "id": "10000000-0000-0000-0000-000000000001",
            "stats": [{
                "id":"lotgd\/core\/level",
                "name":"Level", 
                "type":"CharacterStatInt",
                "value":1
            }, {
                "id":"lotgd\/core\/attack",
                "name":"Attack",
                "type":"CharacterStatInt",
                "value":1
            }, {
                "id":"lotgd\/core\/defense",
                "name":"Defense",
                "type":"CharacterStatInt",
                "value":1
            },{
                "id":"lotgd\/core\/health",
                "name":"Health",
                "type":"CharacterStatRange",
                "currentValue":10,
                "maxValue":10
            }]
        }
    }
}
JSON;

        /** @var Game $game */
        $game = self::$game;
        $game->getEventManager()->subscribe("#h/lotgd/crate-graphql/characterStats/public#", TestEventProvider::class, "lotgd/test");
        $game->getEntityManager()->flush();

        $results = $this->getQueryResultsAuthorized("c4fEAJLQlaV/47UZl52nAQ==", $query, $jsonVariables);
        $this->assertQueryResult($jsonExpected, $results);

        $game->getEventManager()->unsubscribe("#h/lotgd/crate-graphql/characterStats/public#", TestEventProvider::class, "lotgd/test");
    }

    public function testIfQueryOnStatsReturnPrivateStatsIfUserAuthenticated()
    {
        $query = <<<'EOF'
query CharacterQuery($name: String) {
    character(characterName: $name) {
        id
        stats {
            id
            name
            type
            
            ... on CharacterStatInt {
                value
            }
            
            ... on CharacterStatRange {
                currentValue
                maxValue
            }
        }
    }
}
EOF;

        $jsonVariables = <<<JSON
{
    "name": "DB-Test"
}
JSON;

        $jsonExpected = <<<JSON
{
    "data": {
        "character": {
            "id": "10000000-0000-0000-0000-000000000001",
            "stats": [{
                "id":"lotgd\/core\/level",
                "name":"Level", 
                "type":"CharacterStatInt",
                "value":1
            }, {
                "id":"lotgd\/core\/attack",
                "name":"Attack",
                "type":"CharacterStatInt",
                "value":1
            }, {
                "id":"lotgd\/core\/defense",
                "name":"Defense",
                "type":"CharacterStatInt",
                "value":1
            },{
                "id":"lotgd\/core\/health",
                "name":"Health",
                "type":"CharacterStatRange",
                "currentValue":10,
                "maxValue":10
            }]
        }
    }
}
JSON;

        /** @var Game $game */
        $game = self::$game;
        $game->getEventManager()->subscribe("#h/lotgd/crate-graphql/characterStats/private#", TestEventProvider::class, "lotgd/test");
        $game->getEntityManager()->flush();

        $results = $this->getQueryResultsAuthorized("c4fEAJLQlaV/47UZl52nAQ==", $query, $jsonVariables);
        $this->assertQueryResult($jsonExpected, $results);

        $game->getEventManager()->unsubscribe("#h/lotgd/crate-graphql/characterStats/private#", TestEventProvider::class, "lotgd/test");
    }
}
