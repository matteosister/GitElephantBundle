<?php
/**
 * User: matteo
 * Date: 14/11/11
 * Time: 22.56
 *
 * Just for fun...
 */

namespace Cypress\GitElephantBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class BranchController extends Controller
{
    /**
     * @Route("/tree/{ref}", name="repository_branch")
     * @Template()
     *
     * @param $ref The treeish reference
     */
    public function branchAction($ref)
    {
        $branch = $this->get('git_repository')->getBranch($ref);
        if ($branch == $this->get('git_repository')->getMainBranch()) {
            return $this->redirect($this->generateUrl('repository_root'));
        }
        return array(
            'ref'           => $branch->getName(),
            'repository'    => $this->get('git_repository'),
            'tree'          => $this->get('git_repository')->getTree($branch->getFullRef()),
            'active_branch' => $branch,
        );
    }
}
