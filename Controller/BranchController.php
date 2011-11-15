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
}
