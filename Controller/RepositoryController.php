<?php

namespace Cypress\GitElephantBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class RepositoryController extends Controller
{
    /**
     * @Route("/", name="repository_tree", defaults={"path" = ""})
     * @Template()
     */
    public function treeAction(Request $request)
    {
        return array(
            'tree' => $this->getRepository()->getNestedTree($request->get('path')),
            'status' => $this->getRepository()->getStatus()
        );
    }

    /**
     * @return \GitElephant\Repository
     */
    private function getRepository()
    {
        return $this->get('cypress_git_elephant.repository');
    }
}
