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
        if ($step <= 0) {
            throw new \LogicException('Step has to be greater than zero');
        }
        $i = $start;
        $direction = $stop <=> $start;
        $step = $direction * $step;
        $halt = false;
        while (!$halt) {
            yield $i;
            $i += $step;
            if ((($i - $stop) <=> 0) === $direction) {
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
