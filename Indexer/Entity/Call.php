<?php
/**
 * @author Extensibility DL-X-Extensibility-Team@corp.ebay.com
 */

namespace Indexer\Entity;

class Call
{
    /** @var string */
    public $_onType;

    /** @var string */
    public $_name;

    /** @var int */
    public $_row;

    /** @var bool */
    public $_isMethod = false;

    /** @var string */
    public $_return;

    /**
     * @param string $onType
     * @return Call
     */
    public function setOnType($onType)
    {
        $this->_onType = $onType;
        return $this;
    }

    /**
     * @param string $name
     * @return Call
     */
    public function setName($name)
    {
        $this->_name = $name;
        return $this;
    }

    /**
     * @param string $row
     * @return Call
     */
    public function setRow($row)
    {
        $this->_row = $row;
        return $this;
    }

    /**
     * @param string $return
     * @return Call
     */
    public function setReturn($return)
    {
        $this->_return = $return;
        return $this;
    }

    /**
     * @param bool $isMethod
     * @return Call
     */
    public function setIsMethod($isMethod)
    {
        $this->_isMethod = $isMethod;
        return $this;
    }

    /**
     * @return string
     */
    public function getOnType()
    {
        return $this->_onType;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * @return int
     */
    public function getRow()
    {
        return $this->_row;
    }

    /**
     * @return string
     */
    public function getReturn()
    {
        return $this->_return;
    }
}