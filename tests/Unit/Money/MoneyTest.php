<?php
/**
 * User: alec
 * Date: 05.11.18
 * Time: 23:52
 */

namespace Unit\Money;


use AlecRabbit\Money\Calculator\BcMathCalculator;
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

    /** @test
     * @dataProvider \Unit\DataProviders\MoneyBasicDataProvider::newInstanceBadDataProvider()
     * @param $amount
     * @param $currency
     */
    public function throwsWhenCreatesNewInstance($amount, $currency): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Money($amount, new Currency($currency));
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
    public function equalsToAnotherMoneyInstance($amount, $currency, $second_amount, $second_currency, $equality): void
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
    public function comparesTwoAmounts($expected, $amount, $currency, $second_amount): void
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
    public function multipliesTheAmount($result, $amount, $multiplier): void
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
    public function dividesTheAmount($result, $amount, $divisor): void
    {
        $money = new Money($amount, new Currency('EUR'));
        $money = $money->divide($divisor);

        $this->assertInstanceOf(Money::class, $money);
        $this->assertEquals((string)$result, $money->getAmount());
    }


    /**
     * @test
     */
    public function multipliesTheAmountWithLocaleThatUsesCommaSeparator(): void
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
    public function throwsAnExceptionWhenOperandIsInvalidDuringMultiplication($operand): void
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
    public function throwsAnExceptionWhenOperandIsInvalidDuringDivision($operand): void
    {
        $money = new Money(1, new Currency('EUR'));
        $this->expectException(\InvalidArgumentException::class);
        $money->divide($operand);
    }

    /**
     * @test
     */
    public function throwsAnExceptionWhenOperandIsZero(): void
    {
        $money = new Money(1, new Currency('EUR'));
        $this->expectException(\InvalidArgumentException::class);
        $money->divide(0);
    }

    /**
     * @test
     */
    public function throwsAnExceptionWhenRatioIsIsEmptyArray(): void
    {
        $money = new Money(1, new Currency('EUR'));
        $this->expectException(\InvalidArgumentException::class);
        $money->allocate([]);
    }

    /**
     * @test
     */
    public function throwsAnExceptionWhenRatioIsZero(): void
    {
        $money = new Money(1, new Currency('EUR'));
        $this->expectException(\InvalidArgumentException::class);
        $money->allocate([0]);
    }

    /**
     * @test
     */
    public function throwsAnExceptionWhenNumberIsNegative(): void
    {
        $money = new Money(1, new Currency('EUR'));
        $this->expectException(\InvalidArgumentException::class);
        $money->allocateTo(-1);
    }

    /**
     * @test
     */
    public function throwsAnExceptionWhenNumberIsZero(): void
    {
        $money = new Money(1, new Currency('EUR'));
        $this->expectException(\InvalidArgumentException::class);
        $money->allocateTo(0);
    }


    /**
     * @test
     */
    public function throwsAnExceptionWhenRatioIsNegative(): void
    {
        $money = new Money(1, new Currency('EUR'));
        $this->expectException(\InvalidArgumentException::class);
        $money->allocate([2, -1]);
    }

    /**
     * @dataProvider \Unit\DataProviders\MoneyBasicDataProvider::allocationDataProvider()
     * @test
     * @param $amount
     * @param $ratios
     * @param $results
     * @param $precision
     */
    public function allocatesAmount($amount, $ratios, $results, $precision): void
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
    public function allocatesAmountToNTargets($amount, $target, $results, $precision): void
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
    public function hasComparators($amount, $isZero, $isPositive, $isNegative): void
    {
        $money = new Money($amount, new Currency('EUR'));

        $this->assertEquals($isZero, $money->isZero());
        $this->assertEquals($isPositive, $money->isPositive());
        $this->assertEquals($isNegative, $money->isNegative());
    }

    /**
     * @dataProvider \Unit\DataProviders\MoneyBasicDataProvider::comparatorDataProviderTwo()
     * @test
     * @param $amount
     * @param $isNotZero
     * @param $isNotPositive
     * @param $isNotNegative
     */
    public function hasComparatorsTwo($amount, $isNotZero, $isNotPositive, $isNotNegative): void
    {
        $money = new Money($amount, new Currency('EUR'));

        $this->assertEquals($isNotZero, $money->isNotZero());
        $this->assertEquals($isNotPositive, $money->isNotPositive());
        $this->assertEquals($isNotNegative, $money->isNotNegative());
    }

    /**
     * @dataProvider \Unit\DataProviders\MoneyBasicDataProvider::absoluteDataProvider()
     * @test
     * @param $amount
     * @param $expected
     */
    public function calculatesTheAbsoluteAmount($expected, $amount): void
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
    public function calculatesTheNegativeAmount($amount, $result): void
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
    public function calculatesTheModulusOfAnAmount($left, $right, $expected): void
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
    public function convertsToJson(): void
    {
        $this->assertEquals(
            '{"amount":"350","currency":"EUR"}',
            json_encode(Money::EUR(350))
        );
    }

    /**
     * @test
     */
    public function supportsMaxInt(): void
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
    public function returnsRatioOf(): void
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
    public function throwsWhenCalculatingRatioOfZero(): void
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
    public function calculatesSum($values, $sum): void
    {
        $this->assertEquals($sum, Money::sum(...$values));
    }

    /**
     * @dataProvider \Unit\DataProviders\MoneyBasicDataProvider::minDataProvider()
     * @test
     * @param $values
     * @param $min
     */
    public function calculatesMin($values, $min): void
    {
        $this->assertEquals($min, Money::min(...$values));
    }

    /**
     * @dataProvider \Unit\DataProviders\MoneyBasicDataProvider::maxDataProvider()
     * @test
     * @param $values
     * @param $max
     */
    public function calculatesMax($values, $max): void
    {
        $this->assertEquals($max, Money::max(...$values));
    }

    /**
     * @dataProvider \Unit\DataProviders\MoneyBasicDataProvider::avgDataProvider()
     * @test
     * @param $values
     * @param $avg
     */
    public function calculatesAvg($values, $avg): void
    {
        $this->assertEquals($avg, Money::avg(...$values));
    }

    /**
     * @test
     */
    public function throwsWhenCalculatingMinWithZeroArguments(): void
    {
        $this->expectException(\Throwable::class);
        Money::min(...[]);
    }

    /**
     * @test
     */
    public function throwsWhenCalculatingMaxWithZeroArguments(): void
    {
        $this->expectException(\Throwable::class);
        Money::max(...[]);
    }

    /**
     * @test
     */
    public function throwsWhenCalculatingSumWithZeroArguments(): void
    {
        $this->expectException(\Throwable::class);
        Money::sum(...[]);
    }

    /**
     * @test
     */
    public function throwsWhenCalculatingAvgWithZeroArguments(): void
    {
        $this->expectException(\Throwable::class);
        Money::avg(...[]);
    }
}