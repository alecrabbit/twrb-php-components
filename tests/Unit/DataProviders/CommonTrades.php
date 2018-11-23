<?php
/**
 * User: alec
 * Date: 19.11.18
 * Time: 20:09
 */

namespace Tests\Unit\DataProviders;

use AlecRabbit\Circular;
use AlecRabbit\Structures\Trade;

class CommonTrades
{
    public static function generator(
        Circular $type,
        Circular $pair,
        Circular $price,
        Circular $amount,
        $quantity = 5760,
        int $step = 15,
        int $timestamp = 1514764800
    ): ?\Generator {
        while ($quantity-- > 0) {
            yield
            new Trade(
                $type->getElement(),
                $pair->getElement(),
                $price->getElement(),
                $amount->getElement(),
                $timestamp
            );
            $timestamp += $step;
        }
    }


}