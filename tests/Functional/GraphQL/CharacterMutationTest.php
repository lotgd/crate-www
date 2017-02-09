<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\Tests\Functional\GraphQL;

/**
 * Description of CharacterMutationTest
 */
class CharacterMutationTest extends GraphQLTestCase
{
    protected function getSimpleCreationMutation()
    {
        return <<<'GraphQL'
mutation createCharacterMutation($input: CreateCharacterInput!) {
    createCharacter(input: $input) {
        user {
            id
        },
        characterEdge {
            cursor,
            node {
                id,
                name,
                displayName
            }
        }
    }
}
GraphQL;
    }

    protected function getSimpleCreationMutationInput(int $id, string $name, string $mutationId)
    {
        return <<<JSON
{
    "input": {
        "clientMutationId": "$mutationId",
        "userId": "$id",
        "characterName": "$name"
    }
}
JSON;
    }

    public function testIfCharacterCreationWorks()
    {
        $mutation = $this->getSimpleCreationMutation();
        $variables = $this->getSimpleCreationMutationInput(1, "New Player", "asd789g7");

        $result = $this->getQueryResults($mutation, $variables);

        $expectedReturn = [
            "data" => [
                "createCharacter" => [
                    "user" => [
                        "id" => "1"
                    ],
                    "characterEdge" => [
                        "cursor" => $result["data"]["createCharacter"]["characterEdge"]["cursor"],
                        "node" => [
                            "id" => $result["data"]["createCharacter"]["characterEdge"]["node"]["id"],
                            "name" => "New Player",
                            "displayName" => "New Player"
                        ]
                    ]
                ]
            ]
        ];

        $this->assertSame($expectedReturn, $result);
    }

    public function testIfCharacterCreationFailsIfNameIsAlreadyUsed()
    {
        $mutation = $this->getSimpleCreationMutation();
        $variables = $this->getSimpleCreationMutationInput(1, "One", "asd789g7");

        $answer = <<<JSON
{
    "data": {
        "createCharacter": null
    },
    "errors": [
        {
            "message": "User with name One already taken.",
            "locations": [
                {
                    "line": 2,
                    "column": 5
                }
            ],
            "path": [
                "createCharacter"
            ]
        }
    ]
}
JSON;

        $this->assertQuery($mutation, $answer, $variables);
    }
}
