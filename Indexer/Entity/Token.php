<?php
/**
 * @author Extensibility DL-X-Extensibility-Team@corp.ebay.com
 */

namespace Indexer\Entity;

class Token
{
    /** @var string */
    private $_code;

    /** @var string */
    private $_name;

    /** @var string */
    private $_value;

    private $_line;

    /**
     * @param array|string $tokenItem
     */
    public function __construct($tokenItem)
    {
        $this->_code = is_array($tokenItem) ? $tokenItem[0] : 0;
        $this->_name = is_array($tokenItem) ? token_name($tokenItem[0]) : 'CHAR';
        $this->_value = is_array($tokenItem) ? $tokenItem[1] : $tokenItem;
        $this->_line = is_array($tokenItem) ? $tokenItem[2] : null;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->_code;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->_value;
    }

    /**
     * @return string
     */
    public function getLine()
    {
        return $this->_line;
    }
}