<?php

namespace Panlatent\Http;

use BadMethodCallException;
use InvalidArgumentException;

class Context implements ContextInterface
{
    /**
     * @var ContextInterface
     */
    protected $parent;

    /**
     * @return ContextInterface
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @return array|ContextInterface[]
     */
    public function getParents()
    {
        $parents = [];
        for ($parent = $this; $parent = $parent->getParent(); ) {
            $parents[] = $parent;
        }

        return $parents;
    }

    /**
     * @param ContextInterface $context
     */
    public function withParent(ContextInterface $context)
    {
        $this->parent = $context;
    }

    public function __call($name, $arguments)
    {
        if ($this->parent) {
            return call_user_func_array([$this->parent, $name], $arguments);
        }
        throw new BadMethodCallException();
    }

    public function __get($name)
    {
        if ($this->parent) {
            return $this->parent->$name;
        }
        throw new InvalidArgumentException("Invalid context attribute: $name");
    }

    public function __set($name, $value)
    {
        if ($this->parent) {
            return $this->parent->$name = $value;
        }
        throw new InvalidArgumentException("Invalid context attribute: $name");
    }

    public function __isset($name)
    {
        if ($this->parent) {
            return isset($this->parent->$name);
        }
        throw new InvalidArgumentException("Invalid context attribute: $name");
    }

    public function __unset($name)
    {
        if ($this->parent) {
            unset($this->parent->$name);
        }
        throw new InvalidArgumentException("Invalid context attribute: $name");
    }
}