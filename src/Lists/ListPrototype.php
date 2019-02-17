<?php
/**
 * Date: 11.11.18
 * Time: 17:27
 */

namespace AlecRabbit\Lists;

use AlecRabbit\StringList\StringList;

class ListPrototype extends StringList
{
    /**
     * @param string $element
     * @return bool
     */
    public function allowed(string $element): bool
    {
        return parent::has($element);
    }

    /**
     * @param string $element
     * @return bool
     */
    public function notAllowed(string $element): bool
    {
        return !parent::has($element);
    }
}
