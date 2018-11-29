<?php
/**
 * User: alec
 * Date: 27.11.18
 * Time: 13:33
 */

namespace AlecRabbit\Assets\Subclasses;

use AlecRabbit\Assets\Asset;
use AlecRabbit\Currency\Currency;
use AlecRabbit\Money\CalculatorFactory;
use AlecRabbit\Money\Contracts\CalculatorInterface;

class AllocationCalculator
{
    protected const PRECISION = 2;

    /** @var string */
    protected $amount;

    /** @var Currency */
    protected $currency;

    /** @var CalculatorInterface */
    protected $calculator;

    /** @var array */
    private $allocated = [];

    /** @var int */
    private $allocations;

    /** @var string */
    private $remainder;

    /** @var int */
    private $precision;

    /** @var float|int */
    private $total;

    /**
     * AllocationCalculator constructor.
     * @param Asset $param
     */
    public function __construct(Asset $param)
    {
        $this->amount = $param->getAmount();
        $this->currency = $param->getCurrency();
        $this->remainder = $this->amount;
        $this->calculator = CalculatorFactory::getCalculator();
    }

    public function compute(array $ratios, ?int $precision): array
    {
        $this->processArguments($ratios, $precision);

        $this->allocate($ratios);

        $this->allocateRemainder($ratios);
        return
            $this->prepareInstances();
    }

    /**
     * @param array $ratios
     * @param int|null $precision
     */
    private function processArguments(array $ratios, ?int $precision): void
    {
        $this->precision = $precision ?? self::PRECISION;
        $this->computeAllocations($ratios);
        $this->computeTotal($ratios);
    }

    /**
     * @param array $ratios
     */
    private function computeAllocations(array $ratios): void
    {
        if (0 === $this->allocations = \count($ratios)) {
            throw new \InvalidArgumentException('Cannot allocate to none, ratios cannot be an empty array.');
        }
    }

    /**
     * @param array $ratios
     */
    private function computeTotal(array $ratios): void
    {
        if (0 >= $this->total = array_sum($ratios)) {
            throw new \InvalidArgumentException('Sum of ratios must be greater than zero.');
        }
    }

    /**
     * @param array $ratios
     */
    private function allocate(array $ratios): void
    {
        foreach ($ratios as $ratio) {
            $this->checkRatio($ratio);

            $share = $this->calculator->share($this->amount, $ratio, $this->total, $this->precision);
            $this->allocated[] = $share;
            $this->remainder = $this->calculator->subtract($this->remainder, $share);
        }
    }

    /**
     * @param float|int $ratio
     */
    private function checkRatio($ratio): void
    {
        if ($ratio < 0) {
            throw new \InvalidArgumentException('Ratio must be zero or positive.');
        }
    }

    /**
     * @param array $ratios
     */
    private function allocateRemainder(array $ratios): void
    {
        foreach ($this->indexRange() as $index) {
            if (!$ratios[$index]) {
                continue;
            }
            $this->updateAllocated($index);
            break;
        }
    }

    /**
     * @return array
     */
    private function indexRange(): array
    {
        return
            $this->calculator->compare($this->remainder, '0') < 0 ?
                range($this->allocations - 1, 0) : range(0, $this->allocations);
    }

    /**
     * @param int $index
     */
    private function updateAllocated(int $index): void
    {
        $this->allocated[$index] = $this->calculator->add($this->allocated[$index], $this->remainder);
    }

    /**
     * @return array
     */
    private function prepareInstances(): array
    {
        $computed = [];
        foreach ($this->allocated as $amount) {
            $computed[] = new Asset($amount, $this->currency);
        }
        return $computed;
    }
}
