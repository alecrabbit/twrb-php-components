<?php
/**
 * User: alec
 * Date: 11.11.18
 * Time: 15:22
 */

namespace AlecRabbit\StringList\Contracts;

interface StringListInterface
{
    /**
     * @param string ...$including
     * @return self
     */
    public function include(string ...$including): self;

    /**
     * @param string ...$excluding
     * @return self
     */
    public function exclude(string ...$excluding): self;

    /**
     * @param string $element
     * @return bool
     */
    public function has(string $element): bool;
}
