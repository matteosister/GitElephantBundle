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
     * @Route("/branch/new", name="branch_new")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function newAction(Request $request)
    {
        $form = $this->createFormBuilder()
                ->add('name', null, array())
                ->getForm();

        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $data = $form->getData();
                $this->get('cypress_git_elephant.repository')->createBranch($data['name']);
                return $this->redirect($this->generateUrl('repository_branch', array('ref' => $data['name'])));
            }
        }
        return array(
            'form' => $form->createView()
        );
    }

    /**
     * @Route("/tree/{ref}", name="repository_branch")
     *
     * @param $ref
     * @return RedirectResponse
     */
    public function branchAction($ref)
    {
        $this->get('session')->set('gitelephant.branch', $ref);
        return $this->redirect($this->generateUrl('repository_root'));
    }
}
