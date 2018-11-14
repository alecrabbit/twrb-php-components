<?php
/**
 * User: alec
 * Date: 05.11.18
 * Time: 21:06
 */
declare(strict_types=1);

namespace AlecRabbit\Money\Calculator;


use AlecRabbit\Money\Contracts\CalculatorInterface;
use BCMathExtended\BC;

class BcMathCalculator implements CalculatorInterface
{
    private const _SCALE = EXTENDED_SCALE;

    /**
     * @var int
     */
    private $scale;

    /**
     * @param null|int $scale
     */
    public function __construct(?int $scale = null)
    {
        $this->scale = $scale ?? static::_SCALE;
    }

    /**
     * {@inheritdoc}
     */
    public static function supported(): bool
    {
        return \extension_loaded('bcmath');
    }

    /**
     * {@inheritdoc}
     */
    public function compare($a, $b): int
    {
        return BC::comp($a, $b, $this->scale);
    }

    /**
     * {@inheritdoc}
     */
    public function add($amount, $addend): string
    {
        return BC::add($amount, (string)$addend, $this->scale);
    }

    /**
     * {@inheritdoc}
     */
    public function subtract($amount, $subtrahend): string
    {
        return BC::sub($amount, (string)$subtrahend, $this->scale);
    }

    /**
     * {@inheritdoc}
     */
    public function multiply($amount, $multiplier): string
    {
        return BC::mul($amount, (string)$multiplier, $this->scale);
    }

    /**
     * {@inheritdoc}
     */
    public function divide($amount, $divisor): string
    {
        return BC::div($amount, (string)$divisor, $this->scale);
    }

    /**
     * {@inheritdoc}
     */
    public function ceil($number): string
    {
        return BC::ceil((string)$number);
    }

    /**
     * {@inheritdoc}
     */
    public function absolute($number): string
    {
        return BC::abs((string)$number);
    }

    /**
     * {@inheritdoc}
     * @param int $precision
     */
    public function round($number, $precision = 0): string
    {
        return BC::round((string)$number, $precision ?? 0);
    }

    /**
     * {@inheritdoc}
     */
    public function share($amount, $ratio, $total, $precision): string
    {
        return $this->round(BC::div(BC::mul($amount, (string)$ratio, $this->scale), (string)$total, $this->scale), $precision);
    }

    /**
     * {@inheritdoc}
     */
    public function floor($number): string
    {
        return BC::floor($number);
    }

    /**
     * {@inheritdoc}
     */
    public function mod($amount, $divisor): string
    {
        return BC::mod($amount, (string)$divisor, $this->scale);
    }

}