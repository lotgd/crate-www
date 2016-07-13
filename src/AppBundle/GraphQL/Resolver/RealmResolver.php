<?php

namespace LotGD\Crate\WWW\AppBundle\GraphQL\Resolver;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Overblog\GraphQLBundle\Definition\Argument;

class RealmResolver implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function resolveType($type)
    {
        $typeResolver = $this->container->get('overblog_graphql.type_resolver');
        return $typeResolver->resolve("Realm");
    }

    public function resolveRealm(Argument $args = null)
    {
        $crate = [
            'name' => 'Crate Name',
            'version' => '0.1.0-dev',
            'library' => 'lotgd/crate-www',
            'url' => 'http://github.com/lotgd/crate-www',
            'author' => null
        ];
        $core = [
            'name' => 'Core Name',
            'version' => $this->container->get("lotgd.core.game")->getVersion(),
            'library' => 'lotgd/core',
            'url' => 'http://github.com/lotgd/core',
            'author' => null
        ];
        $modules = [];

        // Realm is defined as an object, but arrays work too
        return [
            'name' => 'Test-Environment',
            'configuration' => [
                'crate' => $crate,
                'core' => $core,
                'modules' => $modules
            ]
        ];
    }
}
