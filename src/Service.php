<?php

namespace Katora;

use Interop\Container\ContainerInterface;

class Service
{
    /**
     * @var array
     */
    private $depends;

    /**
     * @var Extension[]
     */
    private $extensions = array();

    /**
     * @var \Closure
     */
    private $closure;

    /**
     * @var object
     */
    private $instance;

    /**
     * @var bool
     */
    private $singleton;

    /**
     * Service constructor.
     * @param \Closure $closure
     * @param array|string|null $depends
     * @param bool $singleton
     */
    function __construct(\Closure $closure, $depends = null, $singleton = false)
    {
        $this->closure = $closure;
        $this->depends = (array) $depends;
        $this->singleton = $singleton;
    }

    /**
     * @param Extension $extension
     */
    public function add(Extension $extension)
    {
        $this->extensions[] = $extension;
    }

    /**
     * @param ContainerInterface $container
     * @return mixed|object
     * @throws \ErrorException
     */
    public function get(ContainerInterface $container)
    {
        if ($this->singleton && ($this->instance != null)) {
            return $this->instance;
        }
        $args = array();
        foreach ($this->depends as $id) {
            $args[] = $container->get($id);
        }
        $closure = $this->closure->bindTo($container);
        $o = call_user_func_array($closure, $args);
        foreach ($this->extensions as $ext) {
            if (($o = $ext->extend($container, $o)) == null) {
                throw new \ErrorException('Extension callback must return a value');
            }
        }
        if ($this->singleton) {
            return $this->instance = $o;
        }
        return $o;
    }
}
