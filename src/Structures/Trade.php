<?php
/**
 * User: alec
 * Date: 19.11.18
 * Time: 14:55
 */

namespace AlecRabbit\Structures;

class Trade
{
    /** @var int */
    public $id;
    /** @var int */
    public $side;
    /** @var string */
    public $pair;
    /** @var float */
    public $amount;
    /** @var float */
    public $price;
    /** @var int|null */
    public $timestamp;

    public function __construct(
        int $side,
        string $pair,
        float $price,
        float $amount,
        ?int $timestamp = null,
        int $id = 0
    ) {
        $this->side = $side;
        $this->pair = $pair;
        $this->amount = $amount;
        $this->price = $price;
        $this->timestamp = $timestamp;
        $this->id = $id;
    }
}
