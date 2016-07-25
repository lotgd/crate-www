<?php

namespace LotGD\Crate\GraphQL\AppBundle\GraphQL\Resolver;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Overblog\GraphQLBundle\Definition\Argument;

class RealmResolver implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function resolveType($type)
    {
        // Resolve the type for the object Type Realm (as defined in config/graphql/Realm.types.yml)
        $typeResolver = $this->container->get('overblog_graphql.type_resolver');
        return $typeResolver->resolve("Realm");
    }

    public function resolveRealm(Argument $args = null)
    {
        $moduleManager = $this->container->get('lotgd.core.game')->getModuleManager();
        $modules = $moduleManager->getModules();

        $core = [
            "name" => "Core",
            "version" => $this->container->get("lotgd.core.game")->getVersion(),
            "library" => "lotgd/core",
            "url" => "https://github.com/lotgd/core.git",
            "author" => "Daenerys Team",
        ];
        $crate = [
            "name" => "Crate",
            "version" => "0.1.0",
            "library" => "lotgd/crate-www",
            "url" => "https://github.com/lotgd/crate-www.git",
            "author" => "Daenerys Team",
        ];

        $outputModules = [];
        foreach ($modules as $module) {
            $outputModules[] = [
                "name" => "",
                "version" => "",
                "library" => $module->getLibrary(),
                "url" => null,
                "author" => null,
            ];
        }

        // Realm is defined as an object, but arrays work too
        return [
            "name" => "Test-Environment",
            "configuration" => [
                'core' => $core,
                'crate' => $crate,
                'modules' => $outputModules,
            ],
        ];
    }
}
