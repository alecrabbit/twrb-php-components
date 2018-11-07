<?php
/**
 * User: alec
 * Date: 06.11.18
 * Time: 15:00
 */

if (!function_exists('trim_zeros')) {
    function trim_zeros(string $numeric)
    {
        return false !== \strpos($numeric, '.') ? \rtrim(\rtrim($numeric, '0'), '.') : $numeric;
    }
}
