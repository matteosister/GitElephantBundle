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
            'ref' => $ref,
            'tree' => $this->getRepository()->getTree($ref),
            'branches' => $this->getRepository()->getBranches()
        );
    }

    /**
     * @Route("/tree/{ref}", name="repository_branch")
     * @Template("CypressGitElephantBundle:Repository:tree.html.twig")
     *
     * @param $ref
     * @return RedirectResponse
     */
    public function branchAction($ref)
    {
        $this->get('session')->set('gitelephant.branch', $ref);
        return $this->redirect($this->generateUrl('repository_root'));
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
            'ref' => $ref,
            'tree' => $this->getRepository()->getTree($ref, $treeish_path),
            'branches' => $this->getRepository()->getBranches()
        );
    }

    /**
     * Get the actual branch name
     *
     * @return string the actual branch name
     */
    private function getActualBranch()
    {
        return $this->get('session')->get('gitelephant.branch', 'master');
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
