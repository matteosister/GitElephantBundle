<?php
/**
 * User: matteo
 * Date: 20/01/12
 * Time: 21.49
 *
 * Just for fun...
 */

namespace Cypress\GitElephantBundle\Collection;

use GitElephant\Repository;

class GitElephantRepositoryCollection implements \ArrayAccess, \Iterator, \Countable
{
    /**
     * the cursor position
     *
     * @var int
     */
    private $position;

    /**
     * @var array
     */
    private $repositories;

    /**
     * Class constructor
     * Accept an array of repository in format:
     * array(
     *     string $name => Repository $class
     * )
     *
     * @param array $repositories an array of repository classes
     */
    public function __construct($repositories, $binary = null)
    {
        $this->position    = 0;

        foreach($repositories as $name => $path) {
            $repository = new Repository($path, $binary);
            $repository->setName($name);
            $this->repositories[] = $repository;
        }
    }

    /**
     * Retrieve a repository by its name
     *
     * @param string $name the repository name
     *
     * @return \GitElephant\Repository $repository
     */
    public function get($name)
    {
        foreach($this->repositories as $repository)
        {
            if ($repository->getName() == $name) {
                return $repository;
            }
        }
        return null;
    }

    /**
     * ArrayAccess interface
     *
     * @param int $offset offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->repositories[$offset]);
    }

    /**
     * ArrayAccess interface
     *
     * @param int $offset offset
     *
     * @return null|mixed
     */
    public function offsetGet($offset)
    {
        return isset($this->repositories[$offset]) ? $this->repositories[$offset] : null;
    }

    /**
     * ArrayAccess interface
     *
     * @param int   $offset offset
     * @param mixed $value  value
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->repositories[] = $value;
        } else {
            $this->repositories[$offset] = $value;
        }
    }

    /**
     * ArrayAccess interface
     *
     * @param int $offset offset
     */
    public function offsetUnset($offset)
    {
        unset($this->repositories[$offset]);
    }

    /**
     * Countable interface
     *
     * @return int|void
     */
    public function count()
    {
        return count($this->repositories);
    }

    /**
     * Iterator interface
     *
     * @return mixed
     */
    public function current()
    {
        return $this->repositories[$this->position];
    }

    /**
     * Iterator interface
     */
    public function next()
    {
        ++$this->position;
    }

    /**
     * Iterator interface
     *
     * @return int
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * Iterator interface
     *
     * @return bool
     */
    public function valid()
    {
        return isset($this->repositories[$this->position]);
    }

    /**
     * Iterator interface
     */
    public function rewind()
    {
        $this->position = 0;
    }
}
