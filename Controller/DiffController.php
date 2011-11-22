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

class DiffController extends Controller
{
    /**
     * @Route("/commit/{ref}", name="repository_diff")
     * @Template()
     *
     * @param $ref The treeish reference
     */
    public function diffAction($ref)
    {
        $commit = $this->get('git_repository')->getCommit($ref);
        var_dump($commit);
        die();
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
