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
    /** @var array */
    private $including = [];
    /** @var array */
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
     * {@inheritdoc}
     */
    public function include(string ...$including): StringListInterface
    {
        $this->including = $this->process($this->including, $including);
        $this->excluding = $this->diff($this->excluding, $this->including);

        return $this;
    }

    /**
     * @param array $first
     * @param array $second
     * @return array
     */
    private function process(array $first, array $second): array
    {
        return
            array_unique(array_merge($first, $second));
    }

    /**
     * @param array $first
     * @param array $second
     * @return array
     */
    private function diff(array $first, array $second): array
    {
        return
            array_diff($first, $second);
    }

    /**
     * {@inheritdoc}
     */
    public function exclude(string ...$excluding): StringListInterface
    {
        $this->excluding = $this->process($this->excluding, $excluding);
        $this->including = $this->diff($this->including, $this->excluding);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $element): bool
    {
        return
            $this->includes($element) && $this->notExcludes($element);
    }

    /**
     * @param string $element
     * @return bool
     */
    private function includes(string $element): bool
    {
        return
            empty($this->including) || \in_array($element, $this->including, true);
    }

    /**
     * @param string $element
     * @return bool
     */
    private function notExcludes(string $element): bool
    {
        return
            empty($this->excluding) || !\in_array($element, $this->excluding, true);
    }
}
