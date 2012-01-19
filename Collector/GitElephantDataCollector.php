<?php
/**
 * User: matteo
 * Date: 19/01/12
 * Time: 21.07
 *
 * Just for fun...
 */

namespace Cypress\GitElephantBundle\Collector;

use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use GitElephant\Repository;

class GitElephantDataCollector extends DataCollector
{
    private $repository;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $this->data = array(
            'branch' => $this->repository->getMainBranch()->getName(),
        );
    }

    public function getBranch()
    {
        return $this->data['branch'];
    }

    public function getName()
    {
        return 'git_elephant';
    }
}
