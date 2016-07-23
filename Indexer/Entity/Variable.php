<?php
/**
 * @author Extensibility DL-X-Extensibility-Team@corp.ebay.com
 */

namespace Indexer\Entity;

class Variable
{
    /** @var string */
    private $_name;

    /** @var string */
    private $_type;

    /**
     * @param $name
     * @return Variable
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
        return $this->_name;
    }

    /**
     * @param string $type
     * @return Variable
     */
    public function setType($type)
    {
        $this->_type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }
}