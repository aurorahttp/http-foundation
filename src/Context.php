<?php

namespace Panlatent\Http;

use BadMethodCallException;

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

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if ($this->parent) {
            return call_user_func_array([$this->parent, $name], $arguments);
        }
        throw new BadMethodCallException();
    }

    /**
     * @param $name
     * @return mixed
     * @throws UnknownPropertyException
     */
    public function __get($name)
    {
        $getter = 'get' . $name;
        if (method_exists($this, $getter)) {
            return $this->$getter();
        } elseif ($this->parent) {
            return $this->parent->$name;
        } elseif (method_exists($this, 'set' . $name)) {
            throw new ProtectedPropertyException('Getting write-only property: ' . get_class($this) . '::' . $name);
        }
        throw new UnknownPropertyException("Invalid context attribute: $name");
    }

    /**
     * @param $name
     * @param $value
     * @throws UnknownPropertyException
     */
    public function __set($name, $value)
    {
        $setter = 'set' . $name;
        if (method_exists($this, $setter)) {
            $this->$setter($value);
        } elseif ($this->parent) {
            $this->parent->$name = $value;
        } elseif (method_exists($this, 'get' . $name)) {
            throw new ProtectedPropertyException('Setting read-only property: ' . get_class($this) . '::' . $name);
        } else {
            throw new UnknownPropertyException('Setting unknown property: ' . get_class($this) . '::' . $name);
        }
    }

    /**
     * @param $name
     * @return bool
     */
    public function __isset($name)
    {
        $getter = 'get' . $name;
        if (method_exists($this, $getter)) {
            return $this->$getter() !== null;
        } elseif ($this->parent) {
            return isset($this->parent->$name);
        }

        return false;
    }

    /**
     * @param $name
     */
    public function __unset($name)
    {
        $setter = 'set' . $name;
        if (method_exists($this, $setter)) {
            $this->$setter(null);
        } elseif ($this->parent) {
            unset($this->parent->$name);
        } elseif (method_exists($this, 'get' . $name)) {
            throw new ProtectedPropertyException('Unset read-only property: ' . get_class($this) . '::' .
                $name);
        }
    }
}