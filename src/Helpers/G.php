<?php
/**
 * User: alec
 * Date: 27.11.18
 * Time: 17:53
 */

namespace AlecRabbit\Helpers;

use AlecRabbit\Rewindable;

class G
{
    /**
     * @param int $start
     * @param int $stop
     * @param int $step
     * @return \Generator
     */
    public static function range(int $start, int $stop, int $step = 1): \Generator
    {
        if ($start < $stop) {
            return self::rangeForward($start, $stop, $step);
        }
        return self::rangeBackwards($start, $stop, $step);
    }

    /**
     * @param int $start
     * @param int $stop
     * @param int $step
     * @return \Generator
     */
    private static function rangeForward(int $start, int $stop, int $step): \Generator
    {
        $i = $start;
        $halt = false;
        while (!$halt) {
            yield $i;
            $i += $step;
            if ($i > $stop) {
                $halt = true;
            }
        }
    }

    /**
     * @param int $start
     * @param int $stop
     * @param int $step
     * @return \Generator
     */
    private static function rangeBackwards(int $start, int $stop, int $step): \Generator
    {
        $i = $start;
        $halt = false;
        while (!$halt) {
            yield $i;
            $i -= $step;
            if ($i < $stop) {
                $halt = true;
            }
        }
    }

    /**
     * @param int $start
     * @param int $stop
     * @param int $step
     * @return Rewindable
     */
    public static function rewindableRange(int $start, int $stop, int $step = 1): Rewindable
    {
        return
            new Rewindable([__CLASS__, 'range'], $start, $stop, $step);
    }
}
