<?php
/**
 * @author Extensibility DL-X-Extensibility-Team@corp.ebay.com
 */

namespace Indexer;

class ReflectionIndexer
{
    private $_fileList = [];

    private $_index = [];

    public function __construct($fileList)
    {
        $this->_fileList = $fileList;
    }

    public function indexFiles()
    {
        $defaultClasses = get_declared_classes();
        foreach ($this->_fileList as $filePath) {
            require_once $filePath;
        }
        foreach (array_diff(get_declared_classes(), $defaultClasses)  as $className) {
            $this->_index[$className] = $this->getClassDetails($className);
        }
    }

    protected function getClassDetails($className)
    {
        $classReflection = new \ReflectionClass($className);
        $methods = $classReflection->getMethods();
        $signatures = [];
        foreach ($methods as $method) {
            if ($method->getDocComment()) {
                $signatures[$method->getName()] =
                    $this->getDocCommentValue($method->getDocComment(), $classReflection->getNamespaceName());

                $paramsInfo = [];
                $parameters = $method->getParameters();
                foreach ($parameters as $parameter) {
                    $paramName = '$' . $parameter->getName();
                    if ($parameter->getClass()) {
                        $paramsInfo[$paramName] = $parameter->getClass()->getName();
                    } else {
                        $paramsInfo[$paramName] = 'UNKNOWN';
                    }

                }
                if (count($parameters)) {
                    $signatures[$method->getName()]['type_hinted'] = $paramsInfo;
                }
            }
        }

        $propertiesInfo = [];
        $properties = $classReflection->getProperties();
        foreach ($properties as $property) {
            $propInfo = [];
            if ($property->getDocComment()) {
                $propInfo =
                    $this->getDocCommentValue($property->getDocComment(), $classReflection->getNamespaceName());
            }
            $propertiesInfo[$property->getName()] = $propInfo;
        }
        return [
            'properties' => $propertiesInfo,
            'methods' => $signatures,
        ];
    }

    public function getDocCommentValue($token, $namespace)
    {
        $matches = [];
        $result = [];
        if (preg_match('/\@var\s+([\\a-zA-Z0-9]+)\s/', $token, $matches)) {
            $result = $this->getFullyQualifiedClassName($namespace, trim($matches[1]));
        }
        $matches = [];
        if (preg_match_all('/\@param\s+([\\\0-9a-zA-Z]+)\s+([\$a-zA-Z0-9]+)\s/', $token, $matches)) {
            for ($i = 0; $i < count($matches[0]); $i++) {
                $result['params'][$matches[2][$i]] =
                    $this->getFullyQualifiedClassName($namespace, trim($matches[1][$i]));
            }

        }
        $matches = [];
        if (preg_match('/\@return\s+([\\\a-zA-Z0-9]+)\s/', $token, $matches)) {
            $result['return'] = $this->getFullyQualifiedClassName($namespace, $matches[1]);
        }
        return $result;
    }

    protected function getFullyQualifiedClassName($namespace, $declaredType)
    {
        if (in_array($declaredType, ['int', 'string', 'null', 'double'])) {
            return $declaredType;
        }
        if ($declaredType[0] == '\\') {
            return trim($declaredType, '\\');
        }
        if (strpos($declaredType, $namespace) !== false) {
            return $declaredType;
        }
        return $namespace . '\\' . $declaredType;
    }

    public function getMethodReturnType($className, $methodName)
    {
        if (isset($this->_index[$className])
            && isset($this->_index[$className]['methods'])
            && isset($this->_index[$className]['methods'][$methodName])
            && isset($this->_index[$className]['methods'][$methodName]['return'])
        ) {
            return $this->_index[$className]['methods'][$methodName]['return'];
        }
    }

    public function getMethodParamType($className, $methodName, $paramName)
    {
        if (isset($this->_index[$className])
            && isset($this->_index[$className]['methods'])
            && isset($this->_index[$className]['methods'][$methodName])
            && isset($this->_index[$className]['methods'][$methodName]['params'])
            && isset($this->_index[$className]['methods'][$methodName]['params'][$paramName])
        ) {
            return $this->_index[$className]['methods'][$methodName]['params'][$paramName];
        }
    }

    public function getPropertyType($className, $propertyName)
    {
        if (isset($this->_index[$className])
            && isset($this->_index[$className]['properties'])
            && isset($this->_index[$className]['properties'][$propertyName])
        ) {
            return $this->_index[$className]['properties'][$propertyName];
        }
    }
}

