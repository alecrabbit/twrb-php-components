<?php
/**
 * User: alec
 * Date: 26.11.18
 * Time: 14:35
 */

namespace AlecRabbit\Money;


trait MoneyFunctions
{
    /**
     * @param Money $first
     * @param Money ...$collection
     *
     * @return Money
     */
    public static function min(Money $first, Money ...$collection): Money
    {
        $min = $first;

        foreach ($collection as $money) {
            if ($money->lessThan($min)) {
                $min = $money;
            }
        }

        return $min;
    }

    /**
     * Checks whether the value represented by this object is less than the other.
     *
     * @param Money $other
     *
     * @return bool
     */
    public function lessThan(Money $other): bool
    {
        return $this->compare($other) === -1;
    }

    /**
     * @param Money $first
     * @param Money ...$collection
     *
     * @return Money
     */
    public static function max(Money $first, Money ...$collection): Money
    {
        $max = $first;

        foreach ($collection as $money) {
            if ($money->greaterThan($max)) {
                $max = $money;
            }
        }

        return $max;
    }

    /**
     * Checks whether the value represented by this object is greater than the other.
     *
     * @param Money $other
     *
     * @return bool
     */
    public function greaterThan(Money $other): bool
    {
        return $this->compare($other) === 1;
    }

    /**
     * @param Money $first
     * @param Money ...$collection
     *
     * @return Money
     */
    public static function sum(Money $first, Money ...$collection): Money
    {
        return $first->add(...$collection);
    }



}