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

class CommitController extends Controller
{
    /**
     * @Route("/commit/{ref}", name="repository_commit")
     * @Template()
     *
     * @param $ref The treeish reference
     */
    public function commitAction($ref)
    {
        $commit = $this->get('git_repository')->getCommit($ref);
        $diff = $this->get('git_repository')->getCommitDiff($commit);
        return array(
            'repository'    => $this->get('git_repository'),
            'commit'        => $commit,
            'diff'          => $diff
        );
    }
}
