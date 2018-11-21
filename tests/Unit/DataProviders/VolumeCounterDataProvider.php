<?php
/**
 * User: alec
 * Date: 19.11.18
 * Time: 20:09
 */

namespace Tests\Unit\DataProviders;

use AlecRabbit\Circular;
use AlecRabbit\Structures\Trade;

class VolumeCounterDataProvider
{
    public static function tradesGenerator(
        Circular $type,
        Circular $price,
        Circular $amount,
        $lines = 5760,
        $pair = 'btc_usd',
        int $timestamp = 1514764800,
        int $step = 15
    ): ?\Generator {
        while ($lines-- > 0) {
            yield
            new Trade(
                $type->getElement(),
                $pair,
                $price->getElement(),
                $amount->getElement(),
                $timestamp
            );
            $timestamp += $step;
        }
    }


}