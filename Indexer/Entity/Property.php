<?php
/**
 * @author Extensibility DL-X-Extensibility-Team@corp.ebay.com
 */

namespace Indexer\Entity;

class Property
{
    /**
     * @var string
     */
    private $_name;

    /**
     * @var string
     */
    private $_type;

    /**
     * @var string
     */
    private $_visibility;

    /**
     * @param string $name
     * @param string $type
     * @param string $visibility
     */
    public function __construct($name, $type, $visibility)
    {
        $this->_name = $name;
        $this->_type = $type;
        $this->_visibility = $visibility;
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
    public function getType()
    {
        return $this->_type;
    }

    /**
     * @return string
     */
    public function getVisibility()
    {
        return $this->_visibility;
    }
}