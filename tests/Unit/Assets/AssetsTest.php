<?php
/**
 * User: alec
 * Date: 11.11.18
 * Time: 20:25
 */

namespace Tests\Unit\Assets;

use AlecRabbit\Assets\Assets;
use AlecRabbit\Money\Currency;
use AlecRabbit\Money\Money;
use PHPUnit\Framework\TestCase;

class AssetsTest extends TestCase
{
    /** @var Assets */
    private $assets;

    /**
     * @test
     * @dataProvider creationCheckDataProvider
     * @param $amount
     */
    public function createsCorrectly($amount): void
    {
        $this->assertTrue($this->assets->have($amount));
    }

    /** @test */
    public function subtract(): void
    {
        $this->assertEquals(Money::EUR(10.532), $this->assets->subtract(Money::EUR(1)));
        $this->assertNull($this->assets->subtract(Money::ZEC(1)));
    }

    /** @test */
    public function add(): void
    {
        $this->assertEquals(Money::EUR(12.532), $this->assets->add(Money::EUR(1)));
        $this->assertEquals(Money::USD(222.777), $this->assets->add(Money::USD(1)));
        $this->assertEquals(Money::USDET(161.322), $this->assets->add(Money::USDET(1)));
        $money = Money::ZEC(1);
        $this->assertEquals($money, $this->assets->add($money));
    }

    /** @test */
    public function have(): void
    {
        $this->assertFalse($this->assets->have(Money::ZEC(1)));
    }

    /** @test */
    public function getAsset(): void
    {
        $this->assertNull($this->assets->getAsset(new Currency('Zec')));
    }

    /** @test */
    public function getCurrencies(): void
    {
        $this->assertEquals(
            [
                0 => 'EUR',
                1 => 'BTC',
                2 => 'LTC',
                3 => 'USD',
                4 => 'PPC',
                5 => 'NVC',
                6 => 'USDET',
                7 => 'BTCET',
                8 => 'UAH',
                9 => 'EURET',
            ],
            $this->assets->getCurrencies()
        );
    }

    public function creationCheckDataProvider(): array
    {
        return [
            [Money::EUR(11.532)],
            [Money::UAH(198323.322)],
            [Money::BTC(2.644)],
            [Money::LTC(1.322)],
            [Money::USD(1.322)],
            [Money::PPC(1.322)],
            [Money::NVC(1090.10)],
            [Money::USDET(160.3220)],
            [Money::BTCET(0.63220)],
            [Money::EURET(10.322)],
        ];
    }

    protected function setUp()
    {
        $this->assets = new Assets(
            Money::EUR(10.21),
            Money::EUR(1.3220),
            Money::BTC(1.3220),
            Money::BTC(1.3220),
            Money::LTC(1.3220),
            Money::USD(1.3220),
            Money::PPC(1.3220),
            Money::NVC(1090.10),
            Money::USD(220.455),
            Money::USDET(160.3220),
            Money::BTCET(0.63220),
            Money::UAH(198323.3220),
            Money::EURET(10.3220)
        );
    }

    protected function tearDown()
    {
        unset($this->assets);
    }


}
