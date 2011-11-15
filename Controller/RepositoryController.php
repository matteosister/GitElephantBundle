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
     * @Template("CypressGitElephantBundle:Repository:tree.html.twig")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return array
     */
    public function rootAction(Request $request)
    {
        $ref = $this->getActualBranch();
        return array(
            'ref'      => $ref,
            'tree'     => $this->getRepository()->getTree($ref),
            'branches' => $this->getRepository()->getBranches(),
            'tags'     => $this->getRepository()->getTags()
        );
    }

    /**
     * @Route("/tree/{ref}/{treeish_path}", name="repository_tree", requirements={"treeish_path" = ".+"})
     * @Template()
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param $ref The treeish reference
     * @param $treeish_path the relative path to the content
     * @return array
     */
    public function treeAction(Request $request, $ref, $treeish_path)
    {
        return array(
            'ref'      => $ref,
            'tree'     => $this->getRepository()->getTree($ref, $treeish_path),
            'branches' => $this->getRepository()->getBranches(),
            'tags'     => $this->getRepository()->getTags()
        );
    }

    /**
     * Get the actual branch name
     *
     * @return string the actual branch name
     */
    private function getActualBranch()
    {
        if ($this->get('session')->get('gitelephant.branch') == null) {
            $this->get('session')->set('gitelephant.branch', $this->getRepository()->getMainBranch()->getName());
        }
        return $this->get('session')->get('gitelephant.branch');
    }

    /**
     * Dummy method for PhpStorm autocomplete function
     *
     * @return \GitElephant\Repository
     */
    private function getRepository()
    {
        return $this->get('cypress_git_elephant.repository');
    }
}
