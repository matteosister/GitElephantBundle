<?php

namespace Cypress\GitElephantBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class RepositoryController extends Controller
{
    /**
     * @Route("/", name="repository_root")
     * @Template()
     */
    public function rootAction(Request $request)
    {
        return array(
            'tree' => $this->getRepository()->getTree(),
            'status' => $this->getRepository()->getStatus()
        );
    }

    /**
     * @Route("/tree/{ref}/{treeish_path}", name="repository_tree", requirements={"treeish_path" = ".+"})
     * @Template()
     */
    public function treeAction(Request $request, $ref, $treeish_path)
    {
        return array(
            'tree' => $this->getRepository()->getTree($treeish_path, $ref),
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
