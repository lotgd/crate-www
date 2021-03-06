<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\AppBundle\GraphQL\Connections;

use Generator;

use Doctrine\Common\Collections\Collection;
use Overblog\GraphQLBundle\Definition\Argument;

use LotGD\Core\Game;
use LotGD\Core\Models\Character;
use LotGD\Crate\GraphQL\AppBundle\GraphQL\Types\CharacterType;
use LotGD\Crate\GraphQL\Exceptions\InputException;
use LotGD\Crate\GraphQL\Models\User;

abstract class BaseConnection
{
    private $offset;
    private $limit;
    private $length;
    private $data;

    public static function decodeCursor(string $encoded): int
    {
        $decoded = base64_decode($encoded);

        if (strlen($decoded) < 17 || substr($decoded, 0, 17) !== "collectionOffset:") {
            throw new InputException("The given cursor is not formatted correctly.");
        }

        $offset = intval(substr($decoded, 17));
        return $offset;
    }

    public static function encodeCursor(int $offset): string
    {
        $decoded = "collectionOffset:$offset";
        return base64_encode($decoded);
    }

    protected function setConnectionParameters(Collection $collection, Argument $args, $data)
    {

        $collectionLength = count($collection);

        // Calculate offset etc
        if (isset($args["first"])) {
            if (isset($args["after"])) {
                $offset = static::decodeCursor($args["after"]) + 1;
                $limit = $args["first"] > $collectionLength - $offset ? $collectionLength - $offset : $args["first"];
            } else {
                $offset = 0;
                $limit = $args["first"] > $collectionLength ? $collectionLength : $args["first"];
            }
        } elseif (isset($args["last"])) {
            if (isset($args["before"])) {
                $offsetCursor = static::decodeCursor($args["before"]);
                $offset = $offsetCursor - $args["last"];
            } else {
                $offset = $collectionLength - $args["last"];
            }

            if ($offset < 0) {
                $limit = $args["last"] + $offset;
                $offset = 0;
            } else {
                $limit = $args["last"];
            }
        } else {
            $limit = $collectionLength;
            $offset = 0;
        }

        $this->limit = $limit;
        $this->offset = $offset;
        $this->length = $collectionLength;
        $this->data = $data;
    }

    public function getEdges(): Generator
    {
        for ($i = $this->offset; $i < ($this->offset + $this->limit); $i++) {
            yield [
                "cursor" => static::encodeCursor($i),
                "__data" => $this->data,
            ];
        }
    }

    public function getPageInfo()
    {
        return [
            "hasNextPage" => ($this->length - 1 > $this->offset + $this->limit == true),
            "hasPreviousPage" => ($this->offset > 0 == true),
            "startCursor" => static::encodeCursor($this->offset),
            "endCursor" => static::encodeCursor($this->offset + $this->limit - 1),
        ];
    }
}