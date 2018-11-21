<?php
/**
 * User: alec
 * Date: 20.11.18
 * Time: 15:19
 */

namespace AlecRabbit\Tmp;

use AlecRabbit\Circular;
use AlecRabbit\Structures\Trade;

class TradesGenerator
{
    /** @var Circular */
    public $price;
    /** @var Circular */
    public $type;
    /** @var Circular */
    public $amount;
    /** @var int */
    public $lines;
    /** @var int */
    public $timestamp = 1514764800;
    /** @var int */
    public $step;
    /** @var int */
    public $id = 0;

    /**
     * TradesGenerator constructor.
     * @param int $lines
     * @param int $step
     */
    public function __construct(int $lines = 1000, int $step = 1)
    {
        $this->price = new Circular(
            [10000.0022, 10000.0001, 10000.2022, 10000.0505]
        );
        $this->type = new Circular([T_ASK, T_BID]);
        $this->amount = new Circular([0.001, 0.002, 0.002, 0.001]);
        $this->lines = $lines;
        $this->step = $step;
    }

    public function data(): \Generator
    {
        while ($this->lines-- > 0) {
            yield
            new Trade(
                $this->type->getElement(),
                'btc_usd',
                $this->price->getElement(),
                $this->amount->getElement(),
                $this->timestamp,
                $this->id++
            );
            $this->timestamp += $this->step;
        }
    }

    /**
     * @param array $prices
     * @return TradesGenerator
     */
    public function setPrices(array $prices): TradesGenerator
    {
        $this->price = new Circular($prices);
        return $this;
    }

    /**
     * @param array $amounts
     * @return TradesGenerator
     */
    public function setAmounts(array $amounts): TradesGenerator
    {
        $this->amount = new Circular($amounts);
        return $this;
    }
}
