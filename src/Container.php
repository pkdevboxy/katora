<?php

namespace Katora;

use Interop\Container\ContainerInterface;
use Katora\Exception\ContainerException;
use Katora\Exception\NotFoundException;

/**
 * Class Container
 * @package Katora
 */
class Container implements \ArrayAccess, ContainerInterface, \Countable
{
    /**
     * @var array
     */
    private $values;

    /**
     * Registry constructor.
     * @param array $values
     */
    function __construct(array $values = array())
    {
        $this->values = $values;
    }

    /**
     * @param string $id
     * @param mixed|Service $value
     * @throws ContainerException
     */
    public function add($id, $value)
    {
        if ($this->has($id)) {
            throw new ContainerException("A service with id '{$id}' is already registered in container");
        }
        $this->values[$id] = $value;
    }

    /**
     * @inheritdoc
     */
    public function count()
    {
        return count($this->values);
    }

    /**
     * @param string $id
     * @param \Closure $closure
     * @param array|string|null $depends
     * @throws ContainerException
     * @throws NotFoundException
     */
    public function extend($id, \Closure $closure, $depends = null)
    {
        if ($this->has($id)) {
            if (($s = $this->values[$id]) instanceof Service) {
                /** @var Service $s */
                $s->add(new Extension($closure, $depends));
            } else {
                throw new ContainerException(sprintf("Service id '%s' is not a %s\\Service instance", $id, __NAMESPACE__));
            }
        } else {
            throw new NotFoundException($id);
        }
    }

    /**
     * @param string $id
     * @param \Closure $closure
     * @param array|string|null $depends
     */
    public function factory($id, \Closure $closure, $depends = null)
    {
        $this->add($id, new Service($closure, $depends));
    }

    /**
     * @inheritdoc
     */
    public function get($id)
    {
        if ($this->has($id)) {
            $value = $this->values[$id];
            if ($value instanceof Service) {
                return $value->get($this);
            }
            return $value;
        } else {
            throw new NotFoundException($id);
        }
    }

    /**
     * @inheritdoc
     */
    public function has($id)
    {
        return isset($this->values[$id]);
    }

    /**
     * @inheritdoc
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * @inheritdoc
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * @inheritdoc
     */
    public function offsetSet($offset, $value)
    {
        $this->add($offset, $value);
    }

    /**
     * @inheritdoc
     */
    public function offsetUnset($offset)
    {
        unset($this->values[$offset]);
    }

    /**
     * @param string $id
     * @param \Closure $closure
     * @param array|string|null $depends
     */
    public function singleton($id, \Closure $closure, $depends = null)
    {
        $this->add($id, new Service($closure, $depends, true));
    }
}
