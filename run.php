<?php
/**
 * @author Extensibility DL-X-Extensibility-Team@corp.ebay.com
 */

include __DIR__ . '/Indexer/Finder.php';
include __DIR__ . '/Indexer/ReflectionIndexer.php';

include __DIR__ . '/Indexer/Entity/Call.php';
include __DIR__ . '/Indexer/Entity/Property.php';
include __DIR__ . '/Indexer/Entity/Method.php';
include __DIR__ . '/Indexer/Entity/PhpClass.php';
include __DIR__ . '/Indexer/Entity/Token.php';
include __DIR__ . '/Indexer/Entity/Variable.php';

include __DIR__ . '/Indexer/FileListProcessor.php';
include __DIR__ . '/Indexer/PhpIndexer.php';
include __DIR__ . '/Indexer/PhpIndexerFactory.php';

$path = __DIR__ . '/Tests';

$finder = new Finder();
$fileList = $finder->getAllFiles($path);

$reflectionIndexer = new Indexer\ReflectionIndexer($fileList);
$reflectionIndexer->indexFiles();


$listProcessor = new Indexer\FileListProcessor(
    $reflectionIndexer,
    new Indexer\PhpIndexerFactory()
);

$index = $listProcessor->tokenizeList($fileList);

header('Content-Type: application/json');
echo json_encode($index);

