<?php
/**
 * User: alec
 * Date: 25.06.18
 * Time: 12:07
 */

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
                $arr);
    }
}