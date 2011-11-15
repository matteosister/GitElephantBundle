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

class TagController extends Controller
{
    /**
     * @Route("/tag/{ref}", name="repository_tag")
     * @Template("CypressGitElephantBundle:Repository:tree.html.twig")
     *
     * @param $ref
     * @return RedirectResponse
     */
    public function tagAction($ref)
    {
        $tag = $this->get('git_repository')->getTag($ref);
        return array(
            'ref'      => $tag->getName(),
            'tree'     => $this->get('git_repository')->getTree($tag->getFullRef()),
            'branches' => $this->get('git_repository')->getBranches(),
            'tags'     => $this->get('git_repository')->getTags()
        );
    }
}
