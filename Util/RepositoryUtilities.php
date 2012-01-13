<?php
/**
 * User: matteo
 * Date: 05/01/12
 * Time: 18.42
 *
 * Just for fun...
 */

namespace Cypress\GitElephantBundle\Util;

use GitElephant\Repository;

class RepositoryUtilities
{
    private $repository;
    private $ref;
    private $path;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    public function setReference($reference)
    {
        if (strpos($reference, '/') === false) {
            $this->ref = $reference;
            $this->path = '';
            return;
        }

        if ($this->repository->getBranch($reference) !== null) {
            $this->ref = $reference;
            $this->path = '';
            return;
        }

        if ($this->repository->getTag($reference) !== null) {
            $this->ref = $reference;
            $this->path = '';
            return;
        }

        $slices = explode('/', $reference);
        $test = '';
        for ($i = 0; $i < count($slices); $i++) {
            $test .= $slices[$i];
            $branch = $this->repository->getBranch($test);
            $tag = $this->repository->getTag($test);
            $test .= '/';
            if ($branch !== null) {
                $this->ref = $branch->getName();
                $this->path = ltrim(str_replace($branch->getName(), '', $reference), '/');
                break;
            }
            if ($tag !== null) {
                $this->ref = $tag->getName();
                $this->path = ltrim(str_replace($tag->getName(), '', $reference), '/');
                break;
            }
        }
    }

    public function getRef()
    {
        return $this->ref;
    }

    public function getPath()
    {
        return $this->path;
    }
}
