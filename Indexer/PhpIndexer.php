<?php
/**
 * @author Extensibility DL-X-Extensibility-Team@corp.ebay.com
 */

namespace Indexer;

class PhpIndexer
{
    const CONTEXT_CLASS = 'class';
    const CONTEXT_IN_CLASS = 'in_class';
    const CONTEXT_NAMESPACE = 'namespace';
    const CONTEXT_METHOD_SIGNATURE = 'method_signature';
    const CONTEXT_METHOD_PARAMS = 'method_params';
    const CONTEXT_METHOD = 'method';
    const CONTEXT_IN_METHOD = 'in_method';

    const STATEMENT_RETURN = 'statement_return';
    const STATEMENT_ASSIGN = 'statement_assign';

    const STATE_WAITING_CALL_IS_METHOD = 'waiting_call_is_method';

    /** @var string */
    protected $_context;

    /** @var int */
    protected $_leftCount = 0;

    /** @var int */
    protected $_rightCount = 0;

    /** @var array */
    protected $_docComment;

    /** @var string */
    protected $_visibility;

    /** @var Entity\PhpClass */
    protected $_currentClass;

    /** @var Entity\Method */
    protected $_currentMethod;

    /** @var Entity\Variable */
    protected $_currentVariable;

    /** @var string */
    protected $_namespace;

    /** @var string */
    protected $_statement;

    /** @var string */
    protected $_assignTo;

    /** @var Entity\Variable[] */
    protected $_localVars = [];

    /** @var Entity\Call */
    protected $_currentCall;

    /** @var int */
    protected $_localVarCounter = 0;

    /** @var string */
    protected $_state;

    /** @var \Indexer\ReflectionIndexer */
    protected $_reflectionIndex;

    public function __construct(ReflectionIndexer $reflectionIndex)
    {
        $this->_reflectionIndex = $reflectionIndex;
    }

    public function processClassToken()
    {
        $this->_context = self::CONTEXT_CLASS;
        $this->_currentClass = new Entity\PhpClass();
        $this->_currentClass->setNamespace($this->_namespace);
    }

    /**
     * @param Entity\Token $token
     */
    public function processDocDocument(Entity\Token $token)
    {
        $this->_docComment = $this->_reflectionIndex->getDocCommentValue($token->getValue(), $this->_namespace);
    }

    /**
     * @param Entity\Token $token
     */
    public function processConstantToken(Entity\Token $token)
    {
        switch ($token->getValue()) {
            case '{':
                $this->processLeftCurly();
                break;
            case '}':
                $this->processRightCurly();
                break;
            case '(':
                $this->processLeftBrace();
                break;
            case ')':
                $this->processRightBrace();
                break;
            case ';';
                $this->processStatementTermination();
                break;
            case '=':
                if ($this->_context == self::CONTEXT_IN_METHOD) {
                    $this->_statement = self::STATEMENT_ASSIGN;
                    if ($this->_currentVariable !== null) {
                        $this->_assignTo = $this->_currentVariable->getName();
                    }
                }
                break;
        }
    }

    public function processLeftBrace()
    {
        switch ($this->_context) {
            case self::CONTEXT_METHOD_SIGNATURE:
                $this->_context = self::CONTEXT_METHOD_PARAMS;
                break;
        }
    }

    public function processRightBrace()
    {
        switch ($this->_context) {
            case self::CONTEXT_METHOD_PARAMS:
                $this->_context = self::CONTEXT_METHOD;
                break;
        }
    }

    /**
     * @return null
     */
    public function processLeftCurly()
    {
        $this->_leftCount++;

        switch ($this->_context) {
            case self::CONTEXT_CLASS:
                $this->_context = self::CONTEXT_IN_CLASS;
                break;
            case self::CONTEXT_METHOD:
                $this->_context = self::CONTEXT_IN_METHOD;
                break;
        }
    }

    /**
     * @return null
     */
    public function processRightCurly()
    {
        $this->_rightCount++;
        if ($this->_leftCount == $this->_rightCount + 1) {
            $this->_currentClass->addMethod($this->_currentMethod);
            $this->_currentMethod = null;
            $this->_localVars = [];
            $this->_context = self::CONTEXT_IN_CLASS;
        }
    }

    public function processStatementTermination()
    {
        switch ($this->_context) {
            case self::CONTEXT_NAMESPACE:
                $this->_context = null;
                break;
            case self::CONTEXT_IN_METHOD:
                if ($this->_statement == self::STATEMENT_ASSIGN) {
                    if (!isset($this->_localVars[$this->_assignTo])) {
                        break;
                    }
                    $this->_localVars[$this->_assignTo]->setType($this->_currentCall->getReturn());
                    $this->_currentCall = null;
                }
                break;
        }
        $this->_statement = null;
        $this->_currentVariable = null;
    }

    public function processString(Entity\Token $token)
    {
        switch ($this->_context) {
            case self::CONTEXT_CLASS:
                $this->_currentClass->setName($token->getValue());
                break;
            case self::CONTEXT_NAMESPACE:
                $this->_namespace .= $token->getValue();
                break;
            case self::CONTEXT_METHOD_SIGNATURE:
                $this->_currentMethod->setName($token->getValue());
                break;
            case self::CONTEXT_IN_METHOD:
                if ($this->_currentCall !== false) {
                    $this->_currentCall
                        ->setOnType($this->_currentVariable->getType())
                        ->setName($token->getValue())
                        ->setRow($token->getLine());
                    $this->_currentVariable = null;
                    $this->_state = self::STATE_WAITING_CALL_IS_METHOD;
                }
                break;
        }
    }

    /**
     * @param Entity\Token $token
     */
    public function processVariable(Entity\Token $token)
    {
        switch ($this->_context) {
            case self::CONTEXT_IN_CLASS:
                $property = new Entity\Property($token->getValue(), $this->_docComment, $this->_visibility);
                $this->_currentClass->addProperty($property);
                $this->_visibility = null;
                $this->_docComment = null;
                break;
            case self::CONTEXT_IN_METHOD:
                if (isset($this->_localVars[$token->getValue()])) {
                    $this->_currentVariable = $this->_localVars[$token->getValue()];
                    break;
                }
                $this->_currentVariable = new Entity\Variable();
                //$methodDoc = $this->_currentMethod->getDocComment();
                //$returnType = isset($methodDoc['return']) ? $methodDoc['return'] : 'UNKNOWN';

                if ($token->getValue() == '$this') {
                    $returnType = $this->_currentClass->getName();
                } else {
                    $returnType = $this->_reflectionIndex->getMethodParamType(
                        $this->_currentClass->getName(),
                        $this->_currentMethod->getName(),
                        $token->getValue()
                    );
                }

                $returnType = is_null($returnType) ? 'UNKNOWN' : $returnType;
                $this->_currentVariable->setName($token->getValue())
                    ->setType($returnType);
                $this->_localVars[$token->getValue()] = $this->_currentVariable;
                break;
        }
    }

    /**
     * @return null
     */
    public function processFunction()
    {
        $this->_context = self::CONTEXT_METHOD_SIGNATURE;
        $this->_currentMethod = new Entity\Method();
        $this->_currentMethod->setDocComment($this->_docComment);
    }

    public function finishCallInitialization(Entity\Token $token)
    {
        if ($token->getValue() == '(') {
            $type = $this->_reflectionIndex->getMethodReturnType(
                    $this->_currentCall->getOnType(),
                    $this->_currentCall->getName()
                );
            if ($type == null) {
                $type = 'UNKNOWN';
            }
            $this->_currentCall->setIsMethod(true)
                ->setReturn($type);
        } else {
            $type = $this->_reflectionIndex->getPropertyType(
                $this->_currentCall->getOnType(),
                $this->_currentCall->getName()
            );
            if ($type == null) {
                $type = 'UNKNOWN';
            }
            $this->_currentCall->setIsMethod(false)
                ->setReturn($type);
        }
        $this->_currentVariable = new Entity\Variable();
        $this->_currentVariable->setName('temp_' . $this->_localVarCounter)
            ->setType($this->_currentCall->getReturn());

        $this->_localVarCounter++;
        $this->_currentMethod->addCall($this->_currentCall);
        $this->_state = null;
    }

    /**
     * @param Entity\Token $token
     */
    public function processToken(Entity\Token $token)
    {
        if ($this->_context == self::CONTEXT_IN_METHOD && $this->_state == self::STATE_WAITING_CALL_IS_METHOD) {
            $this->finishCallInitialization($token);
        }
        switch ($token->getCode()) {
            case T_NAMESPACE:
                $this->_context = self::CONTEXT_NAMESPACE;
                break;
            case T_CLASS:
                $this->processClassToken();
                break;
            case T_DOC_COMMENT:
                $this->processDocDocument($token);
                break;
            case T_PRIVATE:
                //no-break
            case T_PROTECTED:
                //no-break
            case T_PUBLIC:
                $this->_visibility = $token->getValue();
                break;
            case T_STRING:
                $this->processString($token);
                break;
            case T_NS_SEPARATOR:
                if ($this->_context == self::CONTEXT_NAMESPACE) {
                    $this->_namespace .= $token->getValue();
                }
                break;
            case 0:
                $this->processConstantToken($token);
                break;
            case T_WHITESPACE:
                break;
            case T_VARIABLE:
                $this->processVariable($token);
                break;
            case T_FUNCTION:
                $this->processFunction();
                break;
            case T_RETURN:
                if ($this->_context == self::CONTEXT_IN_METHOD) {
                    $this->_statement = self::STATEMENT_RETURN;
                }
                break;
            case T_OBJECT_OPERATOR:
                if ($this->_context == self::CONTEXT_IN_METHOD) {
                    $this->_currentCall = new Entity\Call();
                }
                break;
        }
    }

    /**
     * @param array $tokens
     * @return null
     */
    public function processFileTokens($tokens)
    {
        foreach ($tokens as $tokenInfo) {
            $token = new Entity\Token($tokenInfo);
            $this->processToken($token);
        }
    }

    public function getIndex()
    {
        return $this->_currentClass;
    }
}