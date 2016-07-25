<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class GraphiQLController extends Controller {
    /**
     * Main controller action.$
     * 
     * This method either displays a landing page informing the user that this is
     * a graphql interface or submits the query to the real controller that manages
     * graphql queries.
     * @Route("/")
     * @param Request $request
     * @return type
     */
    public function indexAction()
    {
        return $this->render(
            $this->getParameter('overblog_graphql.graphiql_template'),
            [
                'endpoint' => $this->generateUrl('lotgd_crate_www_app_graph_endpoint'),
                'versions' => [
                    'graphiql' => $this->getParameter('overblog_graphql.versions.graphiql'),
                    'react' => $this->getParameter('overblog_graphql.versions.react'),
                ],
            ]
        );
    }
}