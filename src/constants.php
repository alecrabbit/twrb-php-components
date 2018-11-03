<?php
/**
 * User: alec
 * Date: 31.10.18
 * Time: 15:25
 */

define('RESOLUTION_01min', 60);
define('RESOLUTION_03min', 180);
define('RESOLUTION_05min', 300);
define('RESOLUTION_15min', 900);
define('RESOLUTION_30min', 1800);
define('RESOLUTION_45min', 2700);
define('RESOLUTION_01hour', 3600);
define('RESOLUTION_02hour', 7200);
define('RESOLUTION_03hour', 10800);
define('RESOLUTION_04hour', 14400);
define('RESOLUTION_01day', 86400);

define('RESOLUTIONS',
    [
        RESOLUTION_01min,
        RESOLUTION_03min,
        RESOLUTION_05min,
        RESOLUTION_15min,
        RESOLUTION_30min,
        RESOLUTION_45min,
        RESOLUTION_01hour,
        RESOLUTION_02hour,
        RESOLUTION_03hour,
        RESOLUTION_04hour,
        RESOLUTION_01day,
    ]
);

define('RESOLUTION_ALIASES',
    [
        RESOLUTION_01min => '01m',
        RESOLUTION_03min => '03m',
        RESOLUTION_05min => '05m',
        RESOLUTION_15min => '15m',
        RESOLUTION_30min => '30m',
        RESOLUTION_45min => '45m',
        RESOLUTION_01hour => '01h',
        RESOLUTION_02hour => '02h',
        RESOLUTION_03hour => '03h',
        RESOLUTION_04hour => '04h',
        RESOLUTION_01day => '01d',
    ]
);

define('NORMAL_SCALE', 9);