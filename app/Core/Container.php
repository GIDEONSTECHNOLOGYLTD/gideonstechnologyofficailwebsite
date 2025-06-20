<?php

namespace App\Core;

use Closure;
use ReflectionClass;
use ReflectionParameter;
use Exception;

/**
 * Simple IoC Container implementation for dependency injection
 */
class Container
{
    /**
     * The container's bindings.
     *
     * @var array
     */
    protected $bindings = [];

    /**
     * The container's instances.
     *
     * @var array
     */
    protected $instances = [];

    /**
     * Register a binding in the container.
     *
     * @param string $abstract
     * @param Closure|string|null $concrete
     * @param bool $shared
     * @return void
     */
    public function bind($abstract, $concrete = null, $shared = false)
    {
        if (is_null($concrete)) {
            $concrete = $abstract;
        }

        $this->bindings[$abstract] = [
            'concrete' => $concrete,
            'shared' => $shared,
        ];
    }

    /**
     * Register a shared binding in the container.
     *
     * @param string $abstract
     * @param Closure|string|null $concrete
     * @return void
     */
    public function singleton($abstract, $concrete = null)
    {
        $this->bind($abstract, $concrete, true);
    }

    /**
     * Register an existing instance as a singleton.
     *
     * @param string $abstract
     * @param mixed $instance
     * @return mixed
     */
    public function instance($abstract, $instance)
    {
        $this->instances[$abstract] = $instance;

        return $instance;
    }

    /**
     * Resolve a given type from the container.
     *
     * @param string $abstract
     * @return mixed
     */
    public function make($abstract)
    {
        // If we have an instance cached, return it
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        // If the type doesn't have a binding, just return a new instance
        if (!isset($this->bindings[$abstract])) {
            return $this->build($abstract);
        }

        // Get the registered concrete resolver
        $concrete = $this->bindings[$abstract]['concrete'];

        // If the concrete is the same as the abstract, build it
        if ($concrete === $abstract) {
            $object = $this->build($concrete);
        } else {
            // Otherwise, make the concrete object
            $object = $this->make($concrete);
        }

        // If this is a shared binding, cache the instance
        if ($this->bindings[$abstract]['shared']) {
            $this->instances[$abstract] = $object;
        }

        return $object;
    }

    /**
     * Instantiate a concrete instance of the given type.
     *
     * @param string $concrete
     * @return mixed
     * @throws Exception
     */
    protected function build($concrete)
    {
        // If the concrete is a Closure, just execute it
        if ($concrete instanceof Closure) {
            return $concrete($this);
        }

        try {
            $reflector = new ReflectionClass($concrete);

            // If the class can't be instantiated, throw an exception
            if (!$reflector->isInstantiable()) {
                throw new Exception("Class {$concrete} is not instantiable");
            }

            // Get the constructor
            $constructor = $reflector->getConstructor();

            // If there's no constructor, just instantiate the class
            if (is_null($constructor)) {
                return new $concrete;
            }

            // Get the constructor parameters
            $parameters = $constructor->getParameters();
            
            // If there are no parameters, just instantiate the class
            if (count($parameters) === 0) {
                return new $concrete;
            }

            // Resolve each of the constructor dependencies
            $dependencies = $this->resolveDependencies($parameters);

            // Instantiate the class with the resolved dependencies
            return $reflector->newInstanceArgs($dependencies);
        } catch (Exception $e) {
            throw new Exception("Unable to build {$concrete}: " . $e->getMessage());
        }
    }

    /**
     * Resolve all dependencies of a given function or method.
     *
     * @param array $parameters
     * @return array
     */
    protected function resolveDependencies(array $parameters)
    {
        $dependencies = [];

        foreach ($parameters as $parameter) {
            // If the parameter has a class type-hint, resolve it from the container
            if ($parameter->getClass()) {
                $dependencies[] = $this->make($parameter->getClass()->name);
            } elseif ($parameter->isDefaultValueAvailable()) {
                // If the parameter has a default value, use it
                $dependencies[] = $parameter->getDefaultValue();
            } else {
                // Otherwise, we can't resolve it
                throw new Exception("Unable to resolve dependency: {$parameter->name}");
            }
        }

        return $dependencies;
    }

    /**
     * Check if a binding exists in the container.
     *
     * @param string $abstract
     * @return bool
     */
    public function has($abstract)
    {
        return isset($this->bindings[$abstract]) || isset($this->instances[$abstract]);
    }

    /**
     * Get a binding from the container.
     *
     * @param string $abstract
     * @return mixed|null
     */
    public function getBinding($abstract)
    {
        return $this->bindings[$abstract] ?? null;
    }

    /**
     * Remove a binding from the container.
     *
     * @param string $abstract
     * @return void
     */
    public function remove($abstract)
    {
        unset($this->bindings[$abstract]);
        unset($this->instances[$abstract]);
    }
}