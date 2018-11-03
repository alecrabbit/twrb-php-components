<?php
/**
 * User: alec
 * Date: 31.10.18
 * Time: 16:33
 */

namespace Unit;


use AlecRabbit\DataOHLCV;
use PHPUnit\Framework\TestCase;

class DataOHLCVBasicTest extends TestCase
{
    /**
     * @test
     * @dataProvider sizes
     * @param $expected
     * @param $actual
     */
    public function basicSize($expected, $actual): void
    {
        $ohlcv = new DataOHLCV('btc_usd', $actual);
        $this->assertEquals($expected, $ohlcv->getSize());
    }
    /**
     * @test
     * @dataProvider sizes
     * @param $expected
     */
    public function basicPair($expected): void
    {
        $ohlcv = new DataOHLCV($expected, 10);
        $this->assertEquals($expected, $ohlcv->getPair());
    }

    public function sizes(): array
    {
        return [
            [500, 500],
            [50, 50],
            [0, 0],
            [999,999],
            [1000, 1000],
            [1001, 1001],
            [1440, 1441],
            [1440, 10000],
        ];
    }

    public function pairs(): array
    {
        return [
            'btc_usd',
            'btc_eur',
            'btc_rur',
            'ltc_btc',
            'ltc_usd',
            'ltc_rur',
            'nmc_btc',
            'usd_rur',
            'eur_usd',
            'nvc_btc',
            'ppc_btc',
            'ltc_eur',
            'nmc_usd',
            'nvc_usd',
            'ppc_usd',
            'eur_rur',
            'dsh_btc',
            'dsh_usd',
            'eth_btc',
            'eth_usd',
            'eth_eur',
            'eth_ltc',
            'eth_rur',
            'dsh_rur',
            'dsh_eur',
            'dsh_ltc',
            'dsh_eth',
            'bch_usd',
            'bch_btc',
            'bch_rur',
            'bch_eur',
            'bch_ltc',
            'bch_eth',
            'bch_dsh',
            'zec_btc',
            'zec_usd',
            'usdet_usd',
            'ruret_rur',
            'euret_eur',
            'btcet_btc',
            'ltcet_ltc',
            'ethet_eth',
            'nmcet_nmc',
            'nvcet_nvc',
            'ppcet_ppc',
            'dshet_dsh',
            'bchet_bch',
            'dsh_zec',
            'eth_zec',
            'bch_zec',
            'zec_ltc',
            'usdt_usd',
            'btc_usdt',
            'xmr_usd',
            'xmr_btc',
            'xmr_eth',
            'xmr_rur',
        ];
    }


}