<?php

declare(strict_types=1);

namespace Framework;

use Framework\Exceptions\ContainerException;
use ReflectionClass;
use ReflectionNamedType;

class Container
{
    private array $definitions = [];
    private array $resolved = [];

    public function addDefinitions(array $new_definitions): void
    {
        $this->definitions = [
            ...$this->definitions,
            ...$new_definitions
        ];
    }

    /**
     * @throws \ReflectionException
     * @throws ContainerException
     */
    public function resolve(string $classname)
    {
       $reflection_class = new ReflectionClass($classname);

       if (!$reflection_class->isInstantiable()) {
           throw new ContainerException("Class {$classname} is not instantiable!");
       }

       if (!$constructor = $reflection_class->getConstructor()) {
           return new $classname;
       }

       $params = $constructor->getParameters();

       if (count($params) === 0) {
           return new $classname;
       }

       $dependencies = [];

        foreach ($params as $param) {
            $name = $param->getName();
            $type = $param->getType();

            if (!$type) {
                throw new ContainerException("Failed to resolve class {$classname} because param {$name} is missing a type a hint.");
            }

            if (!$type instanceof ReflectionNamedType || $type->isBuiltin()) {
                throw new ContainerException("Failed to resolve class {$classname} because invalid param name.");
            }

            $dependencies[] = $this->get($type->getName());
       }

       return $reflection_class->newInstanceArgs($dependencies);
    }

    /**
     * @throws ContainerException
     */
    public function get(string $id)
    {
        if (!array_key_exists($id, $this->definitions)) {
            throw new ContainerException("Class {$id} does not exist in container.");
        }

        if (array_key_exists($id, $this->resolved)) {
            return $this->resolved[$id];
        }

        $factory = $this->definitions[$id];
        $dependency = $factory($this);
        $this->resolved[$id] = $dependency;

        return $dependency;
    }
}
