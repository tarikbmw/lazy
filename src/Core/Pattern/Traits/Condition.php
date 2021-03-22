<?php
namespace Core\Pattern\Traits;

/**
 * Conditions
 * @author tarik
 */
trait Condition
{
    /**
     * Event conditions
     * @var array
     */
    private array     $conditions = [];

    /**
     * Registering new condition with handler
     * @param callable $condition
     * @param callable $handler
     */
    public function registerCondition(callable $condition, callable $handler):void
    {
        $this->conditions[] =
        [
            'condition' => $condition,
            'handler'   => $handler
        ];
    }

    /**
     * Get all conditions
     * @return array
     */
    public function getConditions():array
    {
        return $this->conditions;
    }
}