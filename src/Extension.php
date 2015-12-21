<?php

namespace Katora;

use Interop\Container\ContainerInterface;

class Extension
{
    /**
     * @var array
     */
    private $depends;

    /**
     * @var \Closure
     */
    private $closure;

    /**
     * Service constructor.
     * @param \Closure $closure
     * @param array|string|null $depends
     */
    function __construct(\Closure $closure, $depends = null)
    {
        $this->closure = $closure;
        $this->depends = (array) $depends;
    }

    /**
     * @param ContainerInterface $container
     * @param mixed $object
     * @return mixed
     */
    public function extend(ContainerInterface $container, $object)
    {
        $args = array($object);
        foreach ($this->depends as $id) {
            $args[] = $container->get($id);
        }
        $closure = $this->closure->bindTo($container);
        return call_user_func_array($closure, $args);
    }
}
