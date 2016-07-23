<?php
/**
 * @author Extensibility DL-X-Extensibility-Team@corp.ebay.com
 */

namespace Indexer\Entity;

class PhpClass
{
    /** @var  string */
    private $_namespace;

    /** @var string */
    private $_name;

    /** @var array */
    private $_properties;

    /** @var Method[] */
    private $_methods;

    /**
     * @param string $name
     * @return PhpClass
     */
    public function setName($name)
    {
        $this->_name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->_namespace . '\\' . $this->_name;
    }

    /**
     * @param string $namespace
     * @return PhpClass
     */
    public function setNamespace($namespace)
    {
        $this->_namespace = $namespace;
        return $this;
    }

    /**
     * @return string
     */
    public function getNamespace()
    {
        return $this->_namespace;
    }

    /**
     * @param Property $property
     * @return PhpClass
     */
    public function addProperty(Property $property)
    {
        $this->_properties[$property->getName()] = $property;
        return $this;
    }

    /**
     * @param Method $method
     * @return PhpClass
     */
    public function addMethod(Method $method)
    {
        $this->_methods[$method->getName()] = $method;
        return $this;
    }

    /**
     * @return Method[]
     */
    public function getMethods()
    {
        return $this->_methods;
    }
}