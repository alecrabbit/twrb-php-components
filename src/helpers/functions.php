<?php
/**
 * User: alec
 * Date: 25.06.18
 * Time: 12:07
 */

if (!\defined('HEARTBEAT_0')) {
    define('HEARTBEAT_0', "-\e[1D");
}
if (!\defined('HEARTBEAT_1')) {
    define('HEARTBEAT_1', "\e[0;34m\\\e[1D\e[0;0m");
}
if (!\defined('HEARTBEAT_2')) {
    define('HEARTBEAT_2', "|\e[1D");
}
if (!\defined('HEARTBEAT_3')) {
    define('HEARTBEAT_3', "\e[0;33m/\e[1D\e[0;0m");
}


if (!function_exists('c_avg')) {
    /**
     * @param float $first
     * @param float $second
     * @return float
     */
    function c_avg(float $first, float $second): float
    {
        return
            ($first + $second) / 2;
    }
}

if (!function_exists('c_array_aligned_sub')) {
    /**
     * @param array $first // minuend
     * @param array $second // subtrahend
     * @return array
     */
    function c_array_aligned_sub(array $first, array $second): array
    {
        $result = [];
        foreach ($first as $key => $value) {
            if (\array_key_exists($key, $second)) {
                $result[$key] = $first[$key] - $second[$key];
            }
        }
        return $result;
    }
}

if (!function_exists('c_array_div')) {
    /**
     * @param array $arr
     * @param float $divider
     * @return array
     */
    function c_array_div(array $arr, float $divider): array
    {
        return
            \array_map(
                function ($v) use ($divider) {
                    return
                        $v / $divider;
                },
                $arr
            );
    }
}

if (!function_exists('heartbeat')) {
    /**
     * @return string
     */
    function heartbeat(): string
    {
        static $heartCycle = 0;
        if ($heartCycle >= 4) {
            $heartCycle = 0;
        }
        switch (++$heartCycle) {
            case 1:
                return HEARTBEAT_0;
            case 2:
                return HEARTBEAT_1;
            case 3:
                return HEARTBEAT_2;
        }
        return HEARTBEAT_3;
    }
}
