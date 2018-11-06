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
     * @dataProvider dataForNewInstance
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

    public function dataForNewInstance(): array
    {
        return [
//            [$amount, $currency, $resulted_amount, $resulted_currency],
            [null, 'eUr', '0', 'EUR'],
            [5.2, 'eUr', '5.2', 'EUR'],
            [-0.1232, 'usd', '-0.1232', 'USD'],
            [5, 'usd', '5', 'USD'],
            [5, 'usdEt', '5', 'USDET'],
        ];
    }

    /**
     * @dataProvider equalityExamples
     * @test
     * @param $amount
     * @param $currency
     * @param $second_amount
     * @param $second_currency
     * @param $equality
     */
    public function it_equals_to_another_money($amount, $currency, $second_amount, $second_currency, $equality): void
    {
        $money = new Money($amount, new Currency($currency));

        $this->assertEquals($equality, $money->equals(new Money($second_amount, new Currency($second_currency))));
    }

    public function equalityExamples(): array
    {
        return [
//            [$amount, $currency, $resulted_amount, $resulted_currency, $equality],
            [null, 'eUr', '0', 'EUR', true],
            [6, 'usdEt', '5', 'USDET', false],
            [5.2, 'eUr', '5.2', 'EUR', true],
            [-0.1232, 'usd', '-0.1232', 'USD', true],
            [-0.1232, 'usd', null, 'USD', false],
            [5, 'usd', '5', 'USD', true],
            [5, 'usdEt', '5', 'USDET', true],
            [5, 'usdEt', '5', 'eur', false],

        ];
    }

    /**
     * @dataProvider comparisonExamples
     * @test
     * @param $result
     * @param $amount
     * @param $currency
     * @param $second_amount
     */
    public function it_compares_two_amounts($result, $amount, $currency, $second_amount): void
    {
        $money = new Money($amount, new Currency($currency));
        $other = new Money($second_amount, new Currency($currency));

        $this->assertEquals($result, $money->compare($other));
        $this->assertEquals(1 === $result, $money->greaterThan($other));
        $this->assertEquals(0 <= $result, $money->greaterThanOrEqual($other));
        $this->assertEquals(-1 === $result, $money->lessThan($other));
        $this->assertEquals(0 >= $result, $money->lessThanOrEqual($other));

        if ($result === 0) {
            $this->assertEquals($money, $other);
        } else {
            $this->assertNotEquals($money, $other);
        }
        $other = new Money($second_amount, new Currency($currency . 'o'));
        $this->expectException(\InvalidArgumentException::class);
        $this->assertEquals($result, $money->compare($other));

    }

    public function comparisonExamples(): array
    {
        return [
//            [$result, $amount, $currency, $second_amount]
            [0, 1, 'eur', 1],
            [1, 2, 'eur', 1],
            [-1, 0, 'eur', 1],
        ];
    }


    /**
     * @test
     * @dataProvider multiplyDataProvider
     * @param $result
     * @param $amount
     * @param $multiplier
     */
    public function it_multiplies_the_amount($result, $amount, $multiplier): void
    {
        $money = new Money($amount, new Currency('EUR'));

        $money = $money->multiply($multiplier);

        $this->assertInstanceOf(Money::class, $money);
        $this->assertEquals((string)$result, $money->getAmount());
    }

    /**
     * @test
     * @dataProvider divideDataProvider
     * @param $result
     * @param $amount
     * @param $divisor
     */
    public function it_divides_the_amount($result, $amount, $divisor): void
    {
        $money = new Money($amount, new Currency('EUR'));

        $money = $money->divide($divisor);

        $this->assertInstanceOf(Money::class, $money);
        $this->assertEquals((string)$result, $money->getAmount());
    }


    /**
     * @test
     */
    public function it_multiplies_the_amount_with_locale_that_uses_comma_separator(): void
    {
        $this->setLocale(LC_ALL, 'es_ES.utf8');

        $money = new Money(100, new Currency('EUR'));
        $money = $money->multiply(10 / 100);

        $this->assertInstanceOf(Money::class, $money);
        $this->assertEquals(10, $money->getAmount());
    }

    /**
     * @test
     * @dataProvider invalidOperandExamples
     * @param $operand
     */
    public function it_throws_an_exception_when_operand_is_invalid_during_multiplication($operand): void
    {
        $money = new Money(1, new Currency('EUR'));
        $this->expectException(\InvalidArgumentException::class);
        $money->multiply($operand);
    }
    /**
     * @dataProvider invalidOperandExamples
     * @test
     * @param $operand
     */
    public function it_throws_an_exception_when_operand_is_invalid_during_division($operand): void
    {
        $money = new Money(1, new Currency('EUR'));
        $this->expectException(\InvalidArgumentException::class);
        $money->divide($operand);
    }

    /**
     * @dataProvider allocationExamples
     * @test
     * @param $amount
     * @param $ratios
     * @param $results
     */
    public function it_allocates_amount($amount, $ratios, $results): void
    {
        $money = new Money($amount, new Currency('EUR'));
        $allocated = $money->allocate($ratios);
        /** @var Money $money */
        foreach ($allocated as $key => $money) {
            $compareTo = new Money($results[$key], $money->getCurrency());
            $this->assertEquals($money, $compareTo);
        }
    }


    /**
     * @dataProvider allocationTargetExamples
     * @test
     * @param $amount
     * @param $target
     * @param $results
     */
    public function it_allocates_amount_to_n_targets($amount, $target, $results): void
    {
        $money = new Money($amount, new Currency('EUR'));

        $allocated = $money->allocateTo($target);

        foreach ($allocated as $key => $money) {
            $compareTo = new Money($results[$key], $money->getCurrency());

            $this->assertEquals($money, $compareTo);
        }
    }

    /**
     * @dataProvider comparatorExamples
     * @test
     */
    public function it_has_comparators($amount, $isZero, $isPositive, $isNegative)
    {
        $money = new Money($amount, new Currency('EUR'));

        $this->assertEquals($isZero, $money->isZero());
        $this->assertEquals($isPositive, $money->isPositive());
        $this->assertEquals($isNegative, $money->isNegative());
    }

    /**
     * @dataProvider absoluteExamples
     * @test
     * @param $amount
     * @param $result
     */
    public function it_calculates_the_absolute_amount($amount, $result): void
    {
        $money = new Money($amount, new Currency('EUR'));

        $money = $money->absolute();

        $this->assertEquals($result, $money->getAmount());
    }

    /**
     * @dataProvider negativeExamples
     * @test
     * @param $amount
     * @param $result
     */
    public function it_calculates_the_negative_amount($amount, $result): void
    {
        $money = new Money($amount, new Currency('EUR'));

        $money = $money->negative();

        $this->assertEquals($result, $money->getAmount());
    }

    /**
     * @dataProvider modExamples
     * @test
     * @param $left
     * @param $right
     * @param $expected
     */
    public function it_calculates_the_modulus_of_an_amount($left, $right, $expected): void
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
    public function it_converts_to_json(): void
    {
        $this->assertEquals(
            '{"amount":"350","currency":"EUR"}',
            json_encode(Money::EUR(350))
        );
    }

    /**
     * @test
     */
    public function it_supports_max_int(): void
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
    public function it_returns_ratio_of(): void
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
    public function it_throws_when_calculating_ratio_of_zero(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $currency = new Currency('EUR');
        $zero = new Money(0, $currency);
        $six = new Money(6, $currency);

        $six->ratioOf($zero);
    }

    /**
     * @dataProvider sumExamples
     * @test
     * @param $values
     * @param $sum
     */
    public function it_calculates_sum($values, $sum): void
    {
        $this->assertEquals($sum, Money::sum(...$values));
    }

    /**
     * @dataProvider minExamples
     * @test
     * @param $values
     * @param $min
     */
    public function it_calculates_min($values, $min): void
    {
        $this->assertEquals($min, Money::min(...$values));
    }

    /**
     * @dataProvider maxExamples
     * @test
     * @param $values
     * @param $max
     */
    public function it_calculates_max($values, $max): void
    {
        $this->assertEquals($max, Money::max(...$values));
    }

    /**
     * @dataProvider avgExamples
     * @test
     * @param $values
     * @param $avg
     */
    public function it_calculates_avg($values, $avg): void
    {
        $this->assertEquals($avg, Money::avg(...$values));
    }

    /**
     * @test
     * @requires PHP 7.0
     */
    public function it_throws_when_calculating_min_with_zero_arguments(): void
    {
        $this->expectException(\Throwable::class);
        Money::min(...[]);
    }

    /**
     * @test
     * @requires PHP 7.0
     */
    public function it_throws_when_calculating_max_with_zero_arguments(): void
    {
        $this->expectException(\Throwable::class);
        Money::max(...[]);
    }

    /**
     * @test
     * @requires PHP 7.0
     */
    public function it_throws_when_calculating_sum_with_zero_arguments(): void
    {
        $this->expectException(\Throwable::class);
        Money::sum(...[]);
    }

    /**
     * @test
     * @requires PHP 7.0
     */
    public function it_throws_when_calculating_avg_with_zero_arguments(): void
    {
        $this->expectException(\Throwable::class);
        Money::avg(...[]);
    }

    public function invalidOperandExamples(): array
    {
        return [
            [[]],
            [false],
            ['operand'],
            [null],
            [new \stdClass()],
        ];
    }

    public function allocationExamples(): array
    {
        return [
            [100, [1, 1, 1], [34, 33, 33]],
            [101, [1, 1, 1], [34, 34, 33]],
            [5, [3, 7], [2, 3]],
            [5, [7, 3], [4, 1]],
            [5, [7, 3, 0], [4, 1, 0]],
            [-5, [7, 3], [-3, -2]],
            [5, [0, 7, 3], [0, 4, 1]],
            [5, [7, 0, 3], [4, 0, 1]],
            [5, [0, 0, 1], [0, 0, 5]],
            [5, [0, 3, 7], [0, 2, 3]],
            [0, [0, 0, 1], [0, 0, 0]],
            [2, [1, 1, 1], [1, 1, 0]],
            [1, [1, 1], [1, 0]],
        ];
    }

    public function allocationTargetExamples(): array
    {
        return [
            [15, 2, [8, 7]],
            [10, 2, [5, 5]],
            [15, 3, [5, 5, 5]],
            [10, 3, [4, 3, 3]],
        ];
    }

    public function comparatorExamples(): array
    {
        return [
            [1, false, true, false],
            [0, true, false, false],
            [-1, false, false, true],
            ['1', false, true, false],
            ['0', true, false, false],
            ['-1', false, false, true],
        ];
    }

    public function absoluteExamples(): array
    {
        return [
            [1, 1],
            [0, 0],
            [-1, 1],
            ['1', 1],
            ['0', 0],
            ['-1', 1],
        ];
    }

    public function negativeExamples(): array
    {
        return [
            [1, -1],
            [0, 0],
            [-1, 1],
            ['1', -1],
            ['0', 0],
            ['-1', 1],
        ];
    }

    public function modExamples(): array
    {
        return [
            [11, 5, '1'],
            [9, 3, '0'],
            [1006, 10, '6'],
            [1007, 10, '7'],
        ];
    }

    public function sumExamples(): array
    {
        return [
            [[Money::EUR(5), Money::EUR(10), Money::EUR(15)], Money::EUR(30.000)],
            [[Money::EUR(-5), Money::EUR(-10.000), Money::EUR(-15)], Money::EUR(-30)],
            [[Money::EUR(-5), Money::EUR(10.000), Money::EUR(-15)], Money::EUR(-10.0)],
            [[Money::EUR(0)], Money::EUR(0)],
            [[Money::EUR(0), Money::EUR(0), Money::EUR(0)], Money::EUR(0)],
            [[Money::EUR(0), Money::EUR(0.001), Money::EUR(0)], Money::EUR('0.00100')],
        ];
    }

    public function minExamples(): array
    {
        return [
            [[Money::EUR(5), Money::EUR(10), Money::EUR(15.0032323000)], Money::EUR(5)],
            [[Money::EUR(-5), Money::EUR(-10), Money::EUR(-15)], Money::EUR(-15)],
            [[Money::EUR(0)], Money::EUR(0)],
        ];
    }

    public function maxExamples(): array
    {
        return [
            [[Money::EUR(5), Money::EUR(10), Money::EUR(15.0032323000)], Money::EUR('15.0032323')],
            [[Money::EUR(-5), Money::EUR(-10), Money::EUR(-15)], Money::EUR(-5)],
            [[Money::EUR(0)], Money::EUR(0)],
        ];
    }

    public function avgExamples(): array
    {
        return [
            [[Money::EUR(5), Money::EUR(1.5077), Money::EUR(8.2), Money::EUR(10), Money::EUR(15)], Money::EUR(7.94154)],
            [[Money::EUR(5), Money::EUR(10), Money::EUR(15)], Money::EUR(10)],
            [[Money::EUR(-5), Money::EUR(-10), Money::EUR(-15)], Money::EUR(-10)],
            [[Money::EUR(0)], Money::EUR(0)],
        ];
    }

    public function multiplyDataProvider(): array
    {
        return [
//            [$result, $amount, $multiplier],
            ['1', 1, 1],
            ['1.24124', 1, 1.24124],
            ['7.1955528', 2.22, 3.24124],
            ['7.24267565101206', 2.23453462623452, 3.24124565624525],
        ];
    }
    public function divideDataProvider(): array
    {
        return [
//            [$result, $amount, $divisor],
            ['1', 1, 1],
            ['1.24124', 1.24124, 1],
            ['0.5', 3, 6],
            ['0.68492305413977', 2.22, 3.24124],
            ['0.68940613061185', 2.23453462623452, 3.24124565624525],
        ];
    }

}