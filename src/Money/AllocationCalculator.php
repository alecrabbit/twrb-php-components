<?php
/**
 * User: alec
 * Date: 27.11.18
 * Time: 13:33
 */

namespace AlecRabbit\Money;


use AlecRabbit\Money\Contracts\CalculatorInterface;

class AllocationCalculator
{
    /** @var string */
    protected $amount;

    /** @var Currency */
    protected $currency;

    /** @var CalculatorInterface */
    protected $calculator;

    /**
     * AllocationCalculator constructor.
     * @param Money $param
     */
    public function __construct(Money $param)
    {
        $this->amount = $param->getAmount();
        $this->currency = $param->getCurrency();
        $this->calculator = CalculatorFactory::getCalculator();
    }

    public function compute(array $ratios, ?int $precision): array
    {
        $precision = $precision ?? 2;
        if (0 === $allocations = \count($ratios)) {
            throw new \InvalidArgumentException('Cannot allocate to none, ratios cannot be an empty array.');
        }
        if (0 >= $total = array_sum($ratios)) {
            throw new \InvalidArgumentException('Sum of ratios must be greater than zero.');
        }

        $remainder = $amount = $this->amount;
        $results = [];

        foreach ($ratios as $ratio) {
            if ($ratio < 0) {
                throw new \InvalidArgumentException('Ratio must be zero or positive.');
            }

            $share = $this->calculator->share($amount, $ratio, $total, $precision);
            $results[] = $share;
            $remainder = $this->calculator->subtract($remainder, $share);
        }
        switch ($this->calculator->compare($remainder, '0')) {
            case -1:
                for ($i = $allocations - 1; $i >= 0; $i--) {
                    if (!$ratios[$i]) {
                        continue;
                    }
                    $results[$i] = $this->calculator->add($results[$i], $remainder);
                    break;
                }
                break;
            case 1:
                for ($i = 0; $i < $allocations; $i++) {
                    if (!$ratios[$i]) {
                        continue;
                    }
                    $results[$i] = $this->calculator->add($results[$i], $remainder);
                    break;
                }
                break;
            default:
                break;
        }
        $computed = [];
        foreach ($results as $result) {
            $computed[] = new Money($result, $this->currency);
        }
        return $computed;
    }
}