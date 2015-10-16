<?php
namespace Gomoob\Pushwoosh\Filter;

/**
 * Ported from ZF2
 *
 * @author DutchFrontiers / ZF2
 */
class PredicateSet implements PredicateInterface, \Countable
{
    const COMBINED_BY_AND      = '+';
    const OP_AND               = '+';

    const COMBINED_BY_OR       = '*';
    const OP_OR                = '*';

    const COMBINED_BY_EXCLUDE  = '/';
    const OP_EXCLUDE           = '/';

    protected $defaultCombination = self::COMBINED_BY_AND;
    protected $predicates         = [];

    /**
     * Constructor
     *
     * @param  null|array $predicates
     * @param  string $defaultCombination
     */
    public function __construct(array $predicates = null, $defaultCombination = self::COMBINED_BY_AND)
    {
        $this->defaultCombination = $defaultCombination;
        if ($predicates) {
            foreach ($predicates as $predicate) {
                $this->addPredicate($predicate);
            }
        }
    }

    /**
     * Add predicate to set
     *
     * @param  PredicateInterface $predicate
     * @param  string $combination
     * @return PredicateSet
     */
    public function addPredicate(PredicateInterface $predicate, $combination = null)
    {
        if ($combination === null || !in_array($combination, [self::OP_AND, self::OP_OR, self::OP_EXCLUDE])) {
            $combination = $this->defaultCombination;
        }

        if ($combination == self::OP_OR) {
            $this->orPredicate($predicate);
            return $this;
        }

        $this->andPredicate($predicate);
        return $this;
    }

    /**
     * Return the predicates
     *
     * @return PredicateInterface[]
     */
    public function getPredicates()
    {
        return $this->predicates;
    }

    /**
     * Add predicate using OR operator
     *
     * @param  PredicateInterface $predicate
     * @return PredicateSet
     */
    public function orPredicate(PredicateInterface $predicate)
    {
        $this->predicates[] = [self::OP_OR, $predicate];
        return $this;
    }

    /**
     * Add predicate using AND operator
     *
     * @param  PredicateInterface $predicate
     * @return PredicateSet
     */
    public function andPredicate(PredicateInterface $predicate)
    {
        $this->predicates[] = [self::OP_AND, $predicate];
        return $this;
    }

    /**
     * Add predicate using EXCLUDE operator
     *
     * @param  PredicateInterface $predicate
     * @return PredicateSet
     */
    public function excludePredicate(PredicateInterface $predicate)
    {
        $this->predicates[] = [self::OP_EXCLUDE, $predicate];
        return $this;
    }

    /**
     * Get predicate parts for where statement
     *
     * @return array
     */
    public function getExpressionData()
    {
        $parts = [];
        for ($i = 0, $count = count($this->predicates); $i < $count; $i++) {
            /** @var $predicate PredicateInterface */
            $predicate = $this->predicates[$i][1];

            if ($predicate instanceof PredicateSet) {
                $parts[] = '(';
            }

            $parts = array_merge($parts, $predicate->getExpressionData());

            if ($predicate instanceof PredicateSet) {
                $parts[] = ')';
            }

            if (isset($this->predicates[$i+1])) {
                $parts[] = sprintf(' %s ', $this->predicates[$i+1][0]);
            }
        }
        return $parts;
    }

    /**
     * Get count of attached predicates
     *
     * @return int
     */
    public function count()
    {
        return count($this->predicates);
    }
}