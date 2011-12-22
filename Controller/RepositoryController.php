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
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return array
     */
    public function rootAction(Request $request)
    {
        $ref = $this->get('git_repository')->getMainBranch();
        return array(
            'ref'           => $ref->getName(),
            'repository'    => $this->get('git_repository'),
            'tree'          => $this->get('git_repository')->getTree($ref),
            'active_branch' => $this->get('git_repository')->getMainBranch()
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
        $branch = $this->get('git_repository')->getBranch($ref);
        return array(
            'ref'           => $branch->getName(),
            'repository'    => $this->get('git_repository'),
            'tree'          => $this->get('git_repository')->getTree($branch, $treeish_path),
            'active_branch' => $branch,
        );
    }
}
