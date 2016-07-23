<?php
/**
 * @author Extensibility DL-X-Extensibility-Team@corp.ebay.com
 */

class Finder
{
    /**
     * @param string $path
     * @return array
     */
    public function getAllFiles($path)
    {
        $fileList = [];
        return $this->findPhpFiles($path, $fileList);
    }

    /**
     * @param string $path
     * @param array $fileList
     * @return array
     */
    private function findPhpFiles($path, &$fileList)
    {
        $directoryIterator = new DirectoryIterator($path);
        /** @var SplFileInfo $fileInfo */
        foreach ($directoryIterator as $fileInfo) {
            if ($fileInfo->isDot()) {
                continue;
            }
            if ($fileInfo->isDir()) {
                $this->findPhpFiles($path . '/' . $fileInfo->getFileName(), $fileList);
            } elseif (preg_match('/[A-Z]{1,}[a-zA-Z]+\.php/', $fileInfo->getFileName())) {
                $fileList[] = $path . '/' . $fileInfo->getFileName();
            }
        }
        return $fileList;
    }
}
