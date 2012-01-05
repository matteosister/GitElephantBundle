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
     * @Route("/tree/{reference}", name="repository_tree", requirements={"reference" = ".+"})
     * @Template()
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param $ref The treeish reference
     * @param $treeish_path the relative path to the content
     * @return array
     */
    public function treeAction(Request $request, $reference)
    {
        $utils = $this->get('git_repository.utilities');
        $utils->setReference($reference);
        $branch = $this->get('git_repository')->getBranch($utils->getRef());
        return array(
            'ref'           => $branch->getName(),
            'repository'    => $this->get('git_repository'),
            'tree'          => $this->get('git_repository')->getTree($branch, $utils->getPath()),
            'active_branch' => $branch,
            'active_path'          => $utils->getPath()
        );
    }
}
