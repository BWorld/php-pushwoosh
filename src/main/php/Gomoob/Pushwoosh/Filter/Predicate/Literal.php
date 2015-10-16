<?php
namespace Gomoob\Pushwoosh\Filter\Predicate;

use Gomoob\Pushwoosh\Filter\AbstractExpression;
use Gomoob\Pushwoosh\Filter\PredicateInterface;

/**
 * Ported from ZF2
 *
 * @author DutchFrontiers / ZF2
 */
class Literal extends AbstractExpression implements PredicateInterface
{

    /**
     *
     * @var string
     */
    protected $literal = '';

    /**
     *
     * @param
     *            $literal
     */
    public function __construct($literal = '')
    {
        $this->literal = $literal;
    }

    /**
     *
     * @param string $literal
     * @return Literal
     */
    public function setLiteral($literal)
    {
        $this->literal = $literal;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getLiteral()
    {
        return $this->literal;
    }

    /**
     *
     * @return array
     */
    public function getExpressionData()
    {
        return [
            [
                str_replace('%', '%%', $this->literal),
                [],
                []
            ]
        ];
    }
}