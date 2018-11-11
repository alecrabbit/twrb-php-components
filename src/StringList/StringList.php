<?php
/**
 * User: alec
 * Date: 11.11.18
 * Time: 15:22
 */

namespace AlecRabbit\StringList;


use AlecRabbit\StringList\Contracts\StringListInterface;

class StringList implements StringListInterface
{
    private $including = [];
    private $excluding = [];

    /**
     * StringList constructor.
     * @param null|array $including
     * @param null|array $excluding
     */
    public function __construct(?array $including = null, ?array $excluding = null)
    {
        $this->include(...$including ?? []);
        $this->exclude(...$excluding ?? []);
    }

    /**
     * @param string ...$including
     * @return StringList
     */
    public function include(string ...$including): StringListInterface
    {
        $this->including = $this->process($this->including, $including);
        $this->excluding = $this->diff($this->excluding, $this->including);

        return $this;
    }

    private function process(array $first, array $second): iterable
    {
        return
            array_unique(array_merge($first, $second));
    }

    private function diff(array $first, array $second): iterable
    {
        return
            array_diff($first, $second);
    }

    /**
     * @param string ...$excluding
     * @return StringList
     */
    public function exclude(string ...$excluding): StringListInterface
    {
        $this->excluding = $this->process($this->excluding, $excluding);
        $this->including = $this->diff($this->including, $this->excluding);

        return $this;
    }

    public function has(string $element): bool
    {
        return
            $this->includes($element) && $this->notExcludes($element);
    }

    private function notExcludes(string $element): bool
    {
        return
            empty($this->excluding) || !\in_array($element, $this->excluding, true);
    }

    private function includes(string $element): bool
    {
        return
            empty($this->including) || \in_array($element, $this->including, true);

    }
}