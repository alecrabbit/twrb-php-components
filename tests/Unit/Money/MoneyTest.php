<?php
/**
 * User: alec
 * Date: 05.11.18
 * Time: 23:52
 */

namespace Unit\Money;


use AlecRabbit\Money\Currency;
use AlecRabbit\Money\Money;
use PHPUnit\Framework\TestCase;

class MoneyTest extends TestCase
{
    /** @test
     * @dataProvider \Unit\DataProviders\MoneyBasicDataProvider::newInstanceDataProvider()
     * @param $amount
     * @param $currency
     * @param $resulted_amount
     * @param $resulted_currency
     */
    public function createsNewInstance($amount, $currency, $resulted_amount, $resulted_currency): void
    {
        $money = new Money($amount, new Currency($currency));
        $this->assertEquals($resulted_amount, $money->getAmount());
        $this->assertEquals($resulted_currency, $money->getCurrency());
    }

    /**
     * @dataProvider \Unit\DataProviders\MoneyBasicDataProvider::equalityDataProvider()
     * @test
     * @param $amount
     * @param $currency
     * @param $second_amount
     * @param $second_currency
     * @param $equality
     */
    public function equals_to_another_money($amount, $currency, $second_amount, $second_currency, $equality): void
    {
        $money = new Money($amount, new Currency($currency));

        $this->assertEquals($equality, $money->equals(new Money($second_amount, new Currency($second_currency))));
    }

    /**
     * @dataProvider \Unit\DataProviders\MoneyBasicDataProvider::comparisonDataProvider()
     * @test
     * @param $expected
     * @param $amount
     * @param $currency
     * @param $second_amount
     */
    public function compares_two_amounts($expected, $amount, $currency, $second_amount): void
    {
        $money = new Money($amount, new Currency($currency));
        $other = new Money($second_amount, new Currency($currency));

        $this->assertEquals($expected, $money->compare($other));
        $this->assertEquals(1 === $expected, $money->greaterThan($other));
        $this->assertEquals(0 <= $expected, $money->greaterThanOrEqual($other));
        $this->assertEquals(-1 === $expected, $money->lessThan($other));
        $this->assertEquals(0 >= $expected, $money->lessThanOrEqual($other));

        if ($expected === 0) {
            $this->assertEquals($money, $other);
        } else {
            $this->assertNotEquals($money, $other);
        }
        $other = new Money($second_amount, new Currency($currency . 'o'));
        $this->expectException(\InvalidArgumentException::class);
        $this->assertEquals($expected, $money->compare($other));

    }


    /**
     * @test
     * @dataProvider \Unit\DataProviders\MoneyBasicDataProvider::multiplyDataProvider()
     * @param $result
     * @param $amount
     * @param $multiplier
     */
    public function multiplies_the_amount($result, $amount, $multiplier): void
    {
        $money = new Money($amount, new Currency('EUR'));

        $money = $money->multiply($multiplier);

        $this->assertInstanceOf(Money::class, $money);
        $this->assertEquals((string)$result, $money->getAmount());
    }

    /**
     * @test
     * @dataProvider \Unit\DataProviders\MoneyBasicDataProvider::divideDataProvider()()
     * @param $result
     * @param $amount
     * @param $divisor
     */
    public function divides_the_amount($result, $amount, $divisor): void
    {
        $money = new Money($amount, new Currency('EUR'));
        $money = $money->divide($divisor);

        $this->assertInstanceOf(Money::class, $money);
        $this->assertEquals((string)$result, $money->getAmount());
    }


    /**
     * @test
     */
    public function multiplies_the_amount_with_locale_that_uses_comma_separator(): void
    {
        $this->setLocale(LC_ALL, 'es_ES.utf8');

        $money = new Money(100, new Currency('EUR'));
        $money = $money->multiply(10 / 100);

        $this->assertInstanceOf(Money::class, $money);
        $this->assertEquals(10, $money->getAmount());
    }

    /**
     * @test
     * @dataProvider \Unit\DataProviders\MoneyBasicDataProvider::invalidOperandDataProvider()
     * @param $operand
     */
    public function throws_an_exception_when_operand_is_invalid_during_multiplication($operand): void
    {
        $money = new Money(1, new Currency('EUR'));
        $this->expectException(\InvalidArgumentException::class);
        $money->multiply($operand);
    }
    /**
     * @dataProvider \Unit\DataProviders\MoneyBasicDataProvider::invalidOperandDataProvider()
     * @test
     * @param $operand
     */
    public function throws_an_exception_when_operand_is_invalid_during_division($operand): void
    {
        $money = new Money(1, new Currency('EUR'));
        $this->expectException(\InvalidArgumentException::class);
        $money->divide($operand);
    }

    /**
     * @dataProvider \Unit\DataProviders\MoneyBasicDataProvider::allocationDataProvider()
     * @test
     * @param $amount
     * @param $ratios
     * @param $results
     * @param $precision
     */
    public function allocates_amount($amount, $ratios, $results, $precision): void
    {
        $money = new Money($amount, new Currency('EUR'));
        $allocated = $money->allocate($ratios, $precision);
        /** @var Money $money */
        foreach ($allocated as $key => $money) {
            $compareTo = new Money($results[$key], $money->getCurrency());
            $this->assertEquals($compareTo, $money);
        }
    }


    /**
     * @dataProvider \Unit\DataProviders\MoneyBasicDataProvider::allocationTargetDataProvider()
     * @test
     * @param $amount
     * @param $target
     * @param $results
     * @param $precision
     */
    public function allocates_amount_to_n_targets($amount, $target, $results, $precision): void
    {
        $money = new Money($amount, new Currency('EUR'));

        $allocated = $money->allocateTo($target, $precision);
        foreach ($allocated as $key => $money) {
            $compareTo = new Money($results[$key], $money->getCurrency());
            $this->assertEquals($compareTo, $money);
        }
    }

    /**
     * @dataProvider \Unit\DataProviders\MoneyBasicDataProvider::comparatorDataProvider()
     * @test
     * @param $amount
     * @param $isZero
     * @param $isPositive
     * @param $isNegative
     */
    public function has_comparators($amount, $isZero, $isPositive, $isNegative): void
    {
        $money = new Money($amount, new Currency('EUR'));

        $this->assertEquals($isZero, $money->isZero());
        $this->assertEquals($isPositive, $money->isPositive());
        $this->assertEquals($isNegative, $money->isNegative());
    }

    /**
     * @dataProvider \Unit\DataProviders\MoneyBasicDataProvider::absoluteDataProvider()
     * @test
     * @param $amount
     * @param $expected
     */
    public function calculates_the_absolute_amount($expected, $amount): void
    {
        $money = new Money($amount, new Currency('EUR'));

        $this->assertEquals($expected, $money->absolute()->getAmount());
    }

    /**
     * @dataProvider \Unit\DataProviders\MoneyBasicDataProvider::negativeDataProvider()
     * @test
     * @param $amount
     * @param $result
     */
    public function calculates_the_negative_amount($amount, $result): void
    {
        $money = new Money($amount, new Currency('EUR'));

        $money = $money->negative();

        $this->assertEquals($result, $money->getAmount());
    }

    /**
     * @dataProvider \Unit\DataProviders\MoneyBasicDataProvider::modDataProvider()
     * @test
     * @param $left
     * @param $right
     * @param $expected
     */
    public function calculates_the_modulus_of_an_amount($left, $right, $expected): void
    {
        $money = new Money($left, new Currency('EUR'));
        $rightMoney = new Money($right, new Currency('EUR'));

        $money = $money->mod($rightMoney);

        $this->assertInstanceOf(Money::class, $money);
        $this->assertEquals($expected, $money->getAmount());
    }

    /**
     * @test
     */
    public function converts_to_json(): void
    {
        $this->assertEquals(
            '{"amount":"350","currency":"EUR"}',
            json_encode(Money::EUR(350))
        );
    }

    /**
     * @test
     */
    public function supports_max_int(): void
    {
        $one = new Money(1, new Currency('EUR'));
        $ten = new Money(10, new Currency('EUR'));

        $this->assertInstanceOf(Money::class, new Money(PHP_INT_MAX, new Currency('EUR')));
        $this->assertEquals(
            PHP_INT_MAX,
            (new Money(PHP_INT_MAX, new Currency('EUR')))->add($one)->add($one)->subtract($one)->subtract($one)->getAmount()
        );
        $this->assertEquals(
            PHP_INT_MAX,
            (new Money(PHP_INT_MAX, new Currency('EUR')))->add($ten)->add($one)->subtract($ten)->subtract($one)->getAmount()
        );
    }

    /**
     * @test
     */
    public function returns_ratio_of(): void
    {
        $currency = new Currency('EUR');
        $zero = new Money(0, $currency);
        $three = new Money(3, $currency);
        $six = new Money(6, $currency);

        $this->assertEquals(0, $zero->ratioOf($six));
        $this->assertEquals(0.5, $three->ratioOf($six));
        $this->assertEquals(1, $three->ratioOf($three));
        $this->assertEquals(2, $six->ratioOf($three));
    }

    /**
     * @test
     */
    public function throws_when_calculating_ratio_of_zero(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $currency = new Currency('EUR');
        $zero = new Money(0, $currency);
        $six = new Money(6, $currency);

        $six->ratioOf($zero);
    }

    /**
     * @dataProvider \Unit\DataProviders\MoneyBasicDataProvider::sumDataProvider()
     * @test
     * @param $values
     * @param $sum
     */
    public function calculates_sum($values, $sum): void
    {
        $this->assertEquals($sum, Money::sum(...$values));
    }

    /**
     * @dataProvider \Unit\DataProviders\MoneyBasicDataProvider::minDataProvider()
     * @test
     * @param $values
     * @param $min
     */
    public function calculates_min($values, $min): void
    {
        $this->assertEquals($min, Money::min(...$values));
    }

    /**
     * @dataProvider \Unit\DataProviders\MoneyBasicDataProvider::maxDataProvider()
     * @test
     * @param $values
     * @param $max
     */
    public function calculates_max($values, $max): void
    {
        $this->assertEquals($max, Money::max(...$values));
    }

    /**
     * @dataProvider \Unit\DataProviders\MoneyBasicDataProvider::avgDataProvider()
     * @test
     * @param $values
     * @param $avg
     */
    public function calculates_avg($values, $avg): void
    {
        $this->assertEquals($avg, Money::avg(...$values));
    }

    /**
     * @test
     * @requires PHP 7.0
     */
    public function throws_when_calculating_min_with_zero_arguments(): void
    {
        $this->expectException(\Throwable::class);
        Money::min(...[]);
    }

    /**
     * @test
     * @requires PHP 7.0
     */
    public function throws_when_calculating_max_with_zero_arguments(): void
    {
        $this->expectException(\Throwable::class);
        Money::max(...[]);
    }

    /**
     * @test
     * @requires PHP 7.0
     */
    public function throws_when_calculating_sum_with_zero_arguments(): void
    {
        $this->expectException(\Throwable::class);
        Money::sum(...[]);
    }

    /**
     * @test
     * @requires PHP 7.0
     */
    public function throws_when_calculating_avg_with_zero_arguments(): void
    {
        $this->expectException(\Throwable::class);
        Money::avg(...[]);
    }
}