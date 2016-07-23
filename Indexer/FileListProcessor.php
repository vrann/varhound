<?php
/**
 * @author Extensibility DL-X-Extensibility-Team@corp.ebay.com
 */

namespace Indexer;

class FileListProcessor
{
    private $_reflectionIndexer;

    private $_indexerFactory;

    public function __construct(ReflectionIndexer $reflectionIndex, PhpIndexerFactory $indexerFactory)
    {
        $this->_reflectionIndexer = $reflectionIndex;
        $this->_indexerFactory = $indexerFactory;
    }

    public function tokenizeList($fileList)
    {
        $index = [];
        foreach ($fileList as $filePath) {
            $indexer = $this->_indexerFactory->create($this->_reflectionIndexer);
            $tokens = token_get_all(file_get_contents($filePath));
            $indexer->processFileTokens($tokens);
            $classIndex = $indexer->getIndex();
            if (!is_array($classIndex->getMethods())) {
                continue;
            }
            foreach ($classIndex->getMethods() as $method) {
                $index[$classIndex->getName() . '::' . $method->getName()] = $method->getCalls();
            }
        }
        return $index;
    }
}