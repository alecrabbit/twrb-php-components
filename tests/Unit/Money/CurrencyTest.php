<?php
/**
 * User: alec
 * Date: 05.11.18
 * Time: 20:35
 */

namespace Unit\Money;

use AlecRabbit\Money\Currency;
use PHPUnit\Framework\TestCase;

class CurrencyTest extends TestCase
{
    /** @test */
    public function string(): void
    {
        $c = new Currency('usd');
        $this->assertEquals('USD', (string)$c);
    }

    /** @test */
    public function getCode(): void
    {
        $c = new Currency('usd');
        $this->assertEquals('USD', $c->getCode());
    }

    /** @test */
    public function jsonConversion(): void
    {
        $this->assertEquals('"USD"', json_encode(new Currency('USD')));
    }

    /** @test */
    public function equals(): void
    {
        $c = new Currency('usd');
        $o = new Currency('usd');
        $this->assertTrue($c->equals($o));
    }

    /** @test */
    public function construct(): void
    {
        $this->assertInstanceOf(Currency::class, new Currency('usd'));
    }
}
