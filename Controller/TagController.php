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
     * @Route("/tree-tag/{ref}", name="repository_tag")
     *
     * @param $ref
     * @return RedirectResponse
     */
    public function tagAction($ref)
    {
        $this->get('session')->set('gitelephant.tag', $ref);
        return $this->redirect($this->generateUrl('repository_root'));
    }
}
