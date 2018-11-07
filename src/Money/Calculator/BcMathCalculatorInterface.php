<?php
/**
 * User: alec
 * Date: 05.11.18
 * Time: 21:06
 */

namespace AlecRabbit\Money\Calculator;


use AlecRabbit\Money\Contracts\CalculatorInterface;
use BCMathExtended\BC;

class BcMathCalculatorInterface implements CalculatorInterface
{
    /**
     * @var string
     */
    private $scale;

    /**
     * @param null|int $scale
     */
    public function __construct(?int $scale = null)
    {
        $this->scale = $scale ?? EXTENDED_SCALE;
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
        return BC::add($amount, $addend, $this->scale);
    }

    /**
     * {@inheritdoc}
     */
    public function subtract($amount, $subtrahend): string
    {
        return BC::sub($amount, $subtrahend, $this->scale);
    }

    /**
     * {@inheritdoc}
     */
    public function multiply($amount, $multiplier): string
    {
        return BC::mul($amount, $multiplier, $this->scale);
    }

    /**
     * {@inheritdoc}
     */
    public function divide($amount, $divisor): string
    {
        return BC::div($amount, $divisor, $this->scale);
    }

    /**
     * {@inheritdoc}
     */
    public function ceil($number): string
    {
        return BC::ceil($number);
    }

    /**
     * {@inheritdoc}
     */
    public function absolute($number): string
    {
        return BC::abs($number);
    }

    /**
     * {@inheritdoc}
     */
    public function round($number, $precision = 0): string
    {
        return BC::round($number, $precision ?? 0);
    }

    /**
     * {@inheritdoc}
     */
    public function share($amount, $ratio, $total, $precision): string
    {
        return $this->round(BC::div(BC::mul($amount, $ratio, $this->scale), $total, $this->scale), $precision);
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
        return BC::mod($amount, $divisor, $this->scale);
    }

}