<?php
/**
 * User: alec
 * Date: 19.11.18
 * Time: 20:30
 */

namespace AlecRabbit\Structures;

class Volume
{
    /** @var float */
    public $total = 0;
    /** @var float */
    public $buy = 0;
    /** @var float */
    public $sell = 0;

    /**
     * @param float $total
     * @return Volume
     */
    public function setTotal(float $total): Volume
    {
        $this->total = $total;
        return $this;
    }

    /**
     * @param float $buy
     * @return Volume
     */
    public function setBuy(float $buy): Volume
    {
        $this->buy = $buy;
        return $this;
    }

    /**
     * @param float $sell
     * @return Volume
     */
    public function setSell(float $sell): Volume
    {
        $this->sell = $sell;
        return $this;
    }
}
