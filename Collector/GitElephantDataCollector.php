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
    private $enabled;

    public function __construct($path)
    {
        if ($path == false) {
            $this->enabled = false;
        } else {
            $this->enabled = true;
            $this->repository = new Repository($path);
        }
    }

    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $this->data = array(
            'repository' => $this->repository !== null ? $this->repository : null,
            'enabled' => $this->enabled
        );
    }

    public function getRepository()
    {
        return $this->data['repository'];
    }

    public function getEnabled()
    {
        return $this->data['enabled'];
    }

    public function getName()
    {
        return 'git_elephant';
    }
}
