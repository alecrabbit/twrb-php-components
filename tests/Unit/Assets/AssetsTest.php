<?php
/**
 * User: alec
 * Date: 11.11.18
 * Time: 20:25
 */

namespace Tests\Unit\Assets;

use AlecRabbit\Assets\Asset;
use AlecRabbit\Assets\Assets;
use AlecRabbit\Currency\Currency;
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
        $this->assertEquals(Asset::EUR(10.532), $this->assets->subtract(Asset::EUR(1)));
        $this->assertEquals(Asset::EUR(-0.468), $this->assets->subtract(Asset::EUR(11)));
        $this->assertEquals(Asset::ZEC(-1), $this->assets->subtract(Asset::ZEC(1)));
    }

    /** @test */
    public function take(): void
    {
        $this->assertEquals(Asset::BTC(1.644), $this->assets->take(Asset::BTC(1)));
        $this->expectException(\RuntimeException::class);
        $this->assertEquals(Asset::BTC(-0.356), $this->assets->take(Asset::BTC(3)));
    }

    /** @test */
    public function add(): void
    {
        $this->assertEquals(Asset::EUR(12.532), $this->assets->add(Asset::EUR(1)));
        $this->assertEquals(Asset::USD(222.777), $this->assets->add(Asset::USD(1)));
        $this->assertEquals(Asset::USDET(161.322), $this->assets->add(Asset::USDET(1)));
        $money = Asset::ZEC(1);
        $this->assertEquals($money, $this->assets->add($money));
    }

    /** @test */
    public function have(): void
    {
        $this->assertFalse($this->assets->have(Asset::ZEC(1)));
        $this->assertTrue($this->assets->have(Asset::LTC(1.322)));
        $this->assertTrue($this->assets->have(Asset::EUR(10.322)));
        $this->assertTrue($this->assets->have(Asset::EUR(10.322)));
        $this->assertFalse($this->assets->have(Asset::LTC(1.422)));
    }

    /** @test */
    public function getAsset(): void
    {
        $this->assertEquals(Asset::ZEC(0), $this->assets->getAsset(new Currency('Zec')));
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
            [Asset::EUR(11.532)],
            [Asset::UAH(198323.322)],
            [Asset::BTC(2.644)],
            [Asset::LTC(1.322)],
            [Asset::USD(1.322)],
            [Asset::PPC(1.322)],
            [Asset::NVC(1090.10)],
            [Asset::USDET(160.3220)],
            [Asset::BTCET(0.63220)],
            [Asset::EURET(10.322)],
        ];
    }

    protected function setUp()
    {
        $this->assets = new Assets(
            Asset::EUR(10.21),
            Asset::EUR(1.3220),
            Asset::BTC(1.3220),
            Asset::BTC(1.3220),
            Asset::LTC(1.3220),
            Asset::USD(1.3220),
            Asset::PPC(1.3220),
            Asset::NVC(1090.10),
            Asset::USD(220.455),
            Asset::USDET(160.3220),
            Asset::BTCET(0.63220),
            Asset::UAH(198323.3220),
            Asset::EURET(10.3220)
        );
    }

    protected function tearDown()
    {
        unset($this->assets);
    }


}
