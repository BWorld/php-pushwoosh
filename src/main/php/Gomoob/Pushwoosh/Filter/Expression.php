<?php
namespace Gomoob\Pushwoosh\Filter;

class Expression extends AbstractExpression
{

    /**
     * @const
     */
    const PLACEHOLDER = '?';

    /**
     *
     * @var string
     */
    protected $expression = '';

    /**
     *
     * @var array
     */
    protected $parameters = [];

    /**
     *
     * @var array
     */
    protected $types = [];

    /**
     * Constructor
     *
     * @param string $expression
     * @param int|float|bool|string|array $valueParameter
     */
    public function __construct($expression = null, $valueParameter = null /*[, $valueParameter, ... ]*/)
    {
        if ($expression) {
            $this->setExpression($expression);
        }

        $this->setParameters(is_array($valueParameter) ? $valueParameter : array_slice(func_get_args(), 1));
    }

    /**
     *
     * @param
     *            $expression
     * @return Expression
     * @throws Exception\InvalidArgumentException
     */
    public function setExpression($expression)
    {
        if (! is_string($expression) || $expression == '') {
            throw new Exception\InvalidArgumentException('Supplied expression must be a string.');
        }
        $this->expression = $expression;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getExpression()
    {
        return $this->expression;
    }

    /**
     *
     * @param
     *            $parameters
     * @return Expression
     * @throws Exception\InvalidArgumentException
     */
    public function setParameters($parameters)
    {
        if (! is_scalar($parameters) && ! is_array($parameters)) {
            throw new Exception\InvalidArgumentException('Expression parameters must be a scalar or array.');
        }
        $this->parameters = $parameters;
        return $this;
    }

    /**
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     *
     * @deprecated
     *
     * @param array $types
     * @return Expression
     */
    public function setTypes(array $types)
    {
        $this->types = $types;
        return $this;
    }

    /**
     *
     * @deprecated
     *
     * @return array
     */
    public function getTypes()
    {
        return $this->types;
    }

    /**
     *
     * @return array
     * @throws Exception\RuntimeException
     */
    public function getExpressionData()
    {
        $parameters = (is_scalar($this->parameters)) ? [
            $this->parameters
        ] : $this->parameters;
        $parametersCount = count($parameters);
        $expression = str_replace('%', '%%', $this->expression);

        if ($parametersCount == 0) {
            return [
                str_ireplace(self::PLACEHOLDER, '', $expression)
            ];
        }

        // assign locally, escaping % signs
        $expression = str_replace(self::PLACEHOLDER, '%s', $expression, $count);
        if ($count !== $parametersCount) {
            throw new Exception\RuntimeException(
                'The number of replacements in the expression does not match the number of parameters');
        }
        foreach ($parameters as $parameter) {
            list ($values[], $types[]) = $this->normalizeArgument($parameter, self::TYPE_VALUE);
        }
        return [
            [
                $expression,
                $values,
                $types
            ]
        ];
    }
}