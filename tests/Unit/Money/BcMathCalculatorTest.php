<?php
/**
 * Date: 05.11.18
 * Time: 21:22
 */
declare(strict_types=1);

namespace Unit\Money;


use AlecRabbit\Money\Calculator\BcMathCalculator;
use PHPUnit\Framework\TestCase;
use function AlecRabbit\Helpers\trim_zeros;

/**
 * @requires extension bcmath
 */
class BcMathCalculatorTest extends TestCase
{

    /**
     * @dataProvider additionExamples
     * @test
     * @param $value1
     * @param $value2
     * @param $expected
     * @param $scale
     */
    public function it_adds_two_values_with_scale_set($value1, $value2, $expected, $scale): void
    {
        $this->assertEquals($expected, $this->getCalculator($scale)->add($value1, $value2));
    }

    protected function getCalculator($scale = null): BcMathCalculator
    {
        return new BcMathCalculator($scale);
    }

    /**
     * @dataProvider subtractionExamples
     * @test
     * @param $value1
     * @param $value2
     * @param $expected
     * @param $scale
     */
    public function it_subtracts_a_value_from_another_with_scale_set($value1, $value2, $expected, $scale): void
    {
        $this->assertEquals($expected, $this->getCalculator($scale)->subtract($value1, $value2));
    }

    /**
     * @test
     */
    public function it_compares_numbers_close_to_zero(): void
    {
        $this->assertEquals(1, $this->getCalculator()->compare('1', '0.0005'));
        $this->assertEquals(1, $this->getCalculator()->compare('1', '0.000000000000000000000000005'));
    }

    /**
     * @dataProvider additionExamples
     * @test
     * @param $value1
     * @param $value2
     * @param $expected
     * @param $scale
     */
    public function itAddsTwoValues($value1, $value2, $expected, $scale): void
    {
        $this->assertEquals($expected, $this->getCalculator($scale)->add($value1, $value2));
    }

    /**
     * @dataProvider subtractionExamples
     * @test
     * @param $value1
     * @param $value2
     * @param $expected
     * @param $scale
     */
    public function itSubtractsAValueFromAnother($value1, $value2, $expected, $scale): void
    {
        $this->assertEquals($expected, $this->getCalculator($scale)->subtract($value1, $value2));
    }

    /**
     * @dataProvider multiplicationExamples
     * @test
     * @param $value1
     * @param $value2
     * @param $expected
     * @param $scale
     */
    public function itMultipliesAValueByAnother($value1, $value2, $expected, $scale): void
    {
        // php 7.2 & 7.3 bcmath behavior fix
        $expected = trim_zeros($expected);
        $actual = trim_zeros($this->getCalculator($scale)->multiply($value1, $value2));
        $this->assertEquals($expected, $actual);
    }

    /**
     * @dataProvider divisionExamples
     * @test
     * @param $value1
     * @param $value2
     * @param $expected
     * @param $scale
     */
    public function itDividesAValueByAnother($value1, $value2, $expected, $scale): void
    {
        $result = $this->getCalculator($scale)->divide($value1, $value2);
        $this->assertEquals(substr($expected, 0, \strlen($result)), $result);
    }

    /**
     * @dataProvider divisionExactExamples
     * @test
     * @param $value1
     * @param $value2
     * @param $expected
     * @param $scale
     */
    public function itDividesAValueByAnotherExact($value1, $value2, $expected, $scale): void
    {
        $this->assertEquals($expected, $this->getCalculator($scale)->divide($value1, $value2));
    }

    /**
     * @dataProvider ceilExamples
     * @test
     * @param $value
     * @param $expected
     * @param $scale
     */
    public function itCeilAValue($value, $expected, $scale): void
    {
        $this->assertEquals($expected, $this->getCalculator($scale)->ceil($value));
    }

    /**
     * @dataProvider floorProvider
     * @test
     * @param $value
     * @param $expected
     */
    public function itFloorsAValue($expected, $value): void
    {
        $this->assertEquals($expected, $this->getCalculator()->floor($value));
    }

    public function floorProvider(): array
    {
        return [
            ['0', -0],
            ['-1', -0.5],
            ['-1', -1],
            ['-2', -1.5],
            ['-2', -1.8],
            ['-3', -2.7],
            ['0', 0],
            ['0', 0.5],
            ['1', 1],
            ['1', 1.5],
            ['1', 1.8],
            ['2', 2.7],
            ['0', '-0'],
            ['0', ''],
            ['0', null],
            ['20000', '2/0000'],
            ['-60000', '-6/0000'],
            ['1000000000000000000000000000000', '+1/000000000000000000000000000000'],
            [
                '99999999999999999999999999999999999',
                '99999999999999999999999999999999999.000000000000000000000',
            ],
            [
                '99999999999999999999999999999999999',
                '99999999999999999999999999999999999.999999999999999999999',
            ],
            ['0', '0-'],
            ['100000000000000000000000000000000000', 1.0E+35],
            ['-100000000000000000000000000000000000', -1.0E+35],
            ['0', 3E-8],
            ['0', 1.0E-11],
        ];
    }

    /**
     * @dataProvider absoluteExamples
     * @test
     * @param $value
     * @param $expected
     */
    public function it_calculates_the_absolute_value($expected, $value): void
    {
        $this->assertEquals($expected, $this->getCalculator()->absolute($value));
    }

    /**
     * @dataProvider shareExamples
     * @test
     * @param $value
     * @param $ratio
     * @param $total
     * @param $expected
     * @param $precision
     */
    public function it_shares_a_value($value, $ratio, $total, $expected, $precision): void
    {
        $this->assertEquals($expected, $this->getCalculator($precision)->share($value, $ratio, $total, $precision));
    }

    /**
     * @test
     * @dataProvider roundProvider
     * @param string $expected
     * @param int|float|string $number
     * @param int $precision
     */
    public function it_rounds_a_value($expected, $number, $precision = 0): void
    {
        $number = $this->getCalculator()->round($number, $precision);
        self::assertSame($expected, $number);
    }


    /**
     * @dataProvider compareExamples
     * @test
     * @param $left
     * @param $right
     * @param $expected
     * @param $scale
     */
    public function it_compares_values($left, $right, $expected, $scale): void
    {
        $this->assertEquals($expected, $this->getCalculator($scale)->compare($left, $right));
    }

    /**
     * @dataProvider modProvider
     * @test
     * @param $left
     * @param $right
     * @param $expected
     * @param $scale
     */
    public function it_calculates_the_modulus_of_a_value($expected, $left, $right, $scale): void
    {
        $this->assertEquals($expected, $this->getCalculator($scale)->mod($left, $right));
    }

    public function modProvider(): array
    {
        return [
            ['1', '11', '2', 0],
            ['-1', '-1', '5', 0],
            ['1459434331351930289678', '8728932001983192837219398127471', '1928372132132819737213', 0],
            ['0', 9.9999E-10, 1, 0],
            ['0.50', 10.5, 2.5, 2],
            ['0.50000000000000', 10.5, 2.5, null],
            ['0.8', '10', '9.2', 1],
            ['0.0', '20', '4.0', 1],
            ['0.0', '10.5', '3.5', 1],
            ['0.3', '10.2', '3.3', 1],
            ['-0.000559999', 9.9999E-10, -5.6E-4, 9],
        ];
    }

    public function additionExamples(): array
    {
        return [
            [1, 1, '2', 0],
            [10, 5, '15', 0],
            [10.1, 5.0234, '15.1234', 4],
        ];
    }

    public function subtractionExamples(): array
    {
        return [
            [1, 1, '0', 0],
            [10.2, 5.09, '5.11', 2],
            [10, 5, '5', 0],
        ];
    }

    public function multiplicationExamples(): array
    {
        return [
            [1, 1.5, '1.5', 1],
            [10, 1.2500, '12.50', 2],
            [100, 0.29, '29', 0],
            [100, 0.029, '2.9', 1],
            [100, 0.0029, '0.29', 2],
            [1000, 0.29, '290', 0],
            [1000, 0.029, '29', 0],
            [0.2424, 0.029, '0.00702960', 8],
            [1000, 0.0029, '2.9', 1],
            [2000, 0.0029, '5.8', 1],
            ['1', 0.006597, '0.006597', 6],
        ];
    }

    public function divisionExamples(): array
    {
        return [
            [6, 3, '2', 0],
            [100, 25, '4', 0],
            [2, 4, '0.5', 1],
            [20, 0.5, '40', 0],
            [2, 0.5, '4', 0],
            ['1.72', 1.36, '1.26470588235294', 14],
            [181, 17, '10.64705882352941', 1],
            [98, 28, '3.5', 1],
            [98, 25, '3.92', 2],
            [98, 24, '4.083333333333333', 15],
            [1, 5.1555, '0.19396760740956', 14],
        ];
    }

    public function divisionExactExamples(): array
    {
        return [
            [6, 3, '2', 0],
            [100, 25, '4', 0],
            [2, 4, '0.5', 1],
            [20, 0.5, '40', 0],
            [2, 0.5, '4', 0],
            [98, 28, '3.5', 1],
            [98, 25, '3.92', 2],
        ];
    }

    public function ceilExamples(): array
    {
        return [
            [1.2, '2', 0],
            [-1.2, '-1', 0],
            [2.00, '2', 0],
            [1.2, '2', 5],
            [-1.2, '-1', 2],
            [2.00, '2', 3],
        ];
    }

    public function floorExamples(): array
    {
        return [
            [2.7, '2', 0],
            [-2.7, '-3', 0],
            [2.00, '2', 0],
        ];
    }

    public function absoluteExamples(): array
    {
        return [
            ['1', -1],
            ['1.5', -1.5],
            ['1', '-1'],
            ['1.5', '-1.5'],
            [
                '9999999999999999999999999999999999999999999999999999999',
                '-9999999999999999999999999999999999999999999999999999999',
            ],
            ['0', '-0'],
            ['0', ''],
            ['0', null],
            ['20000', '2/0000'],
            ['60000', '-6/0000'],
            ['1000000000000000000000000000000', '+1/000000000000000000000000000000'],
            ['0', '0-'],
            ['100000000000000000000000000000000000', 1.0E+35],
            ['100000000000000000000000000000000000', -1.0E+35],
            ['0.0000051', -5.1E-6],
        ];
    }

    public function shareExamples(): array
    {
        return [
            [10, 2, 4, '5', 0],
        ];
    }

    public function compareExamples(): array
    {
        return [
            [1, 0, 1, 0],
            [1, 1, 0, 0],
            [0, 1, -1, 0],
            ['1', '0', 1, 0],
            ['1', '1', 0, 0],
            ['0', '1', -1, 0],
            ['1', '0.0005', 1, 0],
            ['1', '0.000000000000000000000000005', 1, 0],
        ];
    }

    public function roundProvider(): array
    {
        return [
            ['3', '3.4'],
            ['4', '3.5'],
            ['4', '3.6'],
            ['2', '1.95583'],
            ['2', '1.95583'],
            ['1.96', '1.95583', 2],
            ['1.956', '1.95583', 3],
            ['1.9558', '1.95583', 4],
            ['1.95583', '1.95583', 5],
            ['1241757', '1241757'],
            ['1241757', '1241757', 5],
            ['-3', '-3.4'],
            ['-4', '-3.5'],
            ['-4', '-3.6'],
            ['123456.745671', '123456.7456713', 6],
            ['1', '1.11'],
            ['1.11', '1.11', 2],
            ['0.1666666666667', '0.1666666666666665', 13],
            ['0', '0.1666666666666665', 0.13],
            ['10', '9.999'],
            ['10.00', '9.999', 2],
            ['0.01', '0.005', 2],
            ['0.02', '0.015', 2],
            ['0.03', '0.025', 2],
            ['0.04', '0.035', 2],
            ['0.05', '0.045', 2],
            ['0.06', '0.055', 2],
            ['0.07', '0.065', 2],
            ['0.08', '0.075', 2],
            ['0.09', '0.085', 2],
            ['77777777777777777777777777777', '77777777777777777777777777777.1'],
            [
                '100000000000000000000000000000000000',
                '99999999999999999999999999999999999.99999999999999999999999999999999991',
            ],
            [
                '99999999999999999999999999999999999',
                '99999999999999999999999999999999999.00000000000000000000000000000000001',
            ],
            ['99999999999999999999999999999999999', '99999999999999999999999999999999999.000000000000000000000'],
            ['0', '-0'],
            ['0', ''],
            ['0', null],
            ['20000', '2/0000'],
            ['-60000', '-6/0000'],
            ['1000000000000000000000000000000', '+1/000000000000000000000000000000'],
            ['0', '0-'],
            ['100000000000000000000000000000000000', 1.0E+35],
            ['-100000000000000000000000000000000000', -1.0E+35],
            ['0', 3E-8],
            ['0', 1.0E-11],
            ['-0.0006', -5.6E-4, 4],
            ['0.0000000010', 9.9999E-10, 10],
        ];
    }

}