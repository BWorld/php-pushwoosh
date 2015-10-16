<?php
namespace Gomoob\Pushwoosh\Filter;

/**
 * Ported from ZF2
 *
 * @author DutchFrontiers / ZF2
 */
abstract class AbstractExpression implements ExpressionInterface
{

    /**
     *
     * @var string[]
     */
    protected $allowedTypes = [
        self::TYPE_IDENTIFIER,
        self::TYPE_LITERAL,
        self::TYPE_VALUE
    ];

    /**
     * Normalize Argument
     *
     * @param mixed $argument
     * @param string $defaultType
     *
     * @return array
     *
     * @throws Exception\InvalidArgumentException
     */
    protected function normalizeArgument($argument, $defaultType = self::TYPE_VALUE)
    {
        if ($argument instanceof ExpressionInterface) {
            return $this->buildNormalizedArgument($argument, self::TYPE_VALUE);
        }

        if (is_scalar($argument) || $argument === null) {
            return $this->buildNormalizedArgument($argument, $defaultType);
        }

        if (is_array($argument)) {
            $value = current($argument);

            if ($value instanceof ExpressionInterface) {
                return $this->buildNormalizedArgument($value, self::TYPE_VALUE);
            }

            $key = key($argument);

            if (is_integer($key) && ! in_array($value, $this->allowedTypes)) {
                return $this->buildNormalizedArgument($value, $defaultType);
            }

            return $this->buildNormalizedArgument($key, $value);
        }

        throw new Exception\InvalidArgumentException(
            sprintf('$argument should be %s or %s or %s or %s or %s, "%s" given', 'null', 'scalar', 'array',
                'Zend\Db\Sql\ExpressionInterface', 'Zend\Db\Sql\SqlInterface',
                is_object($argument) ? get_class($argument) : gettype($argument)));
    }

    /**
     *
     * @param mixed $argument
     * @param string $argumentType
     *
     * @return array
     *
     * @throws Exception\InvalidArgumentException
     */
    private function buildNormalizedArgument($argument, $argumentType)
    {
        if (! in_array($argumentType, $this->allowedTypes)) {
            throw new Exception\InvalidArgumentException(
                sprintf('Argument type should be in array(%s)', implode(',', $this->allowedTypes)));
        }

        return [
            $argument,
            $argumentType
        ];
    }
}