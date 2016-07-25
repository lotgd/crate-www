<?php

namespace Tests\AppBundle\Functional;

use Tests\AppBundle\Functional\WebTestCase;

class LandingPageTest extends WebTestCase
{
    public function testLandingPageHasNoException()
    {
        $client = static::makeClient();
        $path = $this->getUrl('lotgd_crate_graphql_app_graph_endpoint');

        $client->request(
            'GET', $path
        );

        $this->assertStatusCode(200, $client);
    }
}
