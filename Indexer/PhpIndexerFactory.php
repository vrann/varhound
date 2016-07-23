<?php
/**
 * @author Extensibility DL-X-Extensibility-Team@corp.ebay.com
 */
namespace Indexer;

class PhpIndexerFactory
{
    /**
     * @param ReflectionIndexer $reflectionIndex
     * @return PhpIndexer
     */
    public function create(ReflectionIndexer $reflectionIndex)
    {
        return new PhpIndexer($reflectionIndex);
    }
}