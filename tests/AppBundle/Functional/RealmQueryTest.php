<?php

namespace Tests\AppBundle\Functional\Relay;

use Tests\AppBundle\Functional\WebTestCase;

class RealmQueryTest extends WebTestCase
{
    public function testRealmTypeReturnsGeneralInformation()
    {
        $query = <<<EOF
query RealmQuery {
    Realm {
        name,
        configuration {
            core {
                name,
                version,
                library,
                url,
                author
            }
            crate {
                name,
                version,
                library,
                url,
                author
            }
        }
    }
}
EOF;
        $this->assertArrayKeysInQuery($query, "Realm", ["name", "configuration"]);
    }
}
