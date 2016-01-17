<?php

namespace Katora;

use Interop\Container\ContainerInterface;
use Katora\Exception\ContainerException;
use Katora\Exception\NotFoundException;

/**
 * Class Container
 * @package Katora
 */
class Container implements ContainerInterface
{
    /**
     * @var array
     */
    private $singletons = [];

    /**
     * @var array
     */
    private $values;

    /**
     * Registry constructor.
     * @param array $values
     */
    function __construct(array $values = [])
    {
        $this->values = $values;
    }

    /**
     * @param string $id
     * @param \Closure $extension
     * @throws ContainerException
     * @throws NotFoundException
     */
    public function extend($id, \Closure $extension)
    {
        if ($this->has($id)) {
            if (($closure = $this->values[$id]) instanceof \Closure) {
                $this->set($id, function () use ($closure, $extension)
                {
                    /** @var Container $this */
                    $extension = $extension->bindTo($this);
                    $closure = $closure->bindTo($this);
                    return $extension($closure());
                }, true);
            } else {
                throw new ContainerException("Service id '{$id}' is not a \\Closure");
            }
        } else {
            throw new NotFoundException($id);
        }
    }

    /**
     * @inheritdoc
     */
    public function get($id)
    {
        if ($this->has($id)) {
            if (($value = $this->values[$id]) instanceof \Closure) {
                $value = $value->bindTo($this);
                $value = $value();
                if (isset($this->singletons[$id])) {
                    $this->set($id, $value, true);
                }
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
     * @param string $id
     * @param mixed $value
     * @throws ContainerException
     */
    public function set($id, $value, $overwrite = false)
    {
        if (($overwrite === false) && $this->has($id)) {
            throw new ContainerException("A service with id '{$id}' is already registered in container");
        }
        $this->values[$id] = $value;
    }

    /**
     * @param string $id
     * @param \Closure $closure
     */
    public function singleton($id, \Closure $closure)
    {
        $this->set($id, $closure);
        $this->singletons[$id] = true;
    }
}
