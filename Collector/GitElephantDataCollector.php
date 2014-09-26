<?php

namespace Cypress\GitElephantBundle\Collector;

use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use GitElephant\Repository;

/**
 * Class GitElephantDataCollector
 *
 * @category Collector
 * @package  Cypress\GitElephantBundle\Collector
 * @author   Matteo Giachino <https://github.com/matteosister>
 */
class GitElephantDataCollector extends DataCollector
{
    private $repository;
    private $enabled;

    /**
     * Constructor
     *
     * @param $path
     */
    public function __construct($path)
    {
        if ($path == false) {
            $this->enabled = false;
        } else {
            $this->enabled = true;
            $this->repository = new Repository($path);
        }
    }

    /**
     * Collect
     *
     * @param Request    $request
     * @param Response   $response
     * @param \Exception $exception
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $this->data = array(
            'repository' => $this->repository !== null ? $this->repository : null,
            'enabled'    => $this->enabled
        );
    }

    /**
     * Get repository
     *
     * @return mixed
     */
    public function getRepository()
    {
        return $this->data['repository'];
    }

    /**
     * Get enabled
     *
     * @return mixed
     */
    public function getEnabled()
    {
        return $this->data['enabled'];
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return 'git_elephant';
    }
}
