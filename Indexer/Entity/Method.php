<?php
/**
 * @author Extensibility DL-X-Extensibility-Team@corp.ebay.com
 */
namespace Indexer\Entity;

class Method
{
    /** @var string */
    private $_name;

    /** @var string */
    private $_docComment;

    /** @var Call[] */
    private $_calls = [];

    /**
     * @param $name
     * @return Method
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
     * @param string $docComment
     * @return Method
     */
    public function setDocComment($docComment)
    {
        $this->_docComment = $docComment;
        return $this;
    }

    /**
     * @return string
     */
    public function getDocComment()
    {
        return $this->_docComment ;
    }

    /**
     * @param Call $call
     */
    public function addCall(Call $call)
    {
        $this->_calls[] = $call;
    }

    /**
     * @return Call[]
     */
    public function getCalls()
    {
        return $this->_calls;
    }
}