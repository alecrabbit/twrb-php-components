<?php
/**
 * User: alec
 * Date: 11.11.18
 * Time: 15:22
 */

namespace AlecRabbit\StringList;


use AlecRabbit\StringList\Contracts\ListInterface;

class StringList implements ListInterface
{
    private $including;
    private $excluding;

    /**
     * StringList constructor.
     * @param array $including
     * @param array $excluding
     */
    public function __construct(array $including, array $excluding)
    {
        $this->including = $including;
        $this->excluding = $excluding;
    }

    /**
     * @param string ...$including
     * @return StringList
     */
    public function include(string ...$including): StringList
    {
        $this->including = array_unique(array_merge($this->including, $including));
        $this->excluding = array_diff($this->excluding, $this->including);

        return $this;
    }

    /**
     * @param string ...$excluding
     * @return StringList
     */
    public function exclude(string ...$excluding): StringList
    {
        $this->excluding = array_unique(array_merge($this->excluding, $excluding));
        $this->including = array_diff($this->including, $this->excluding);

        return $this;
    }

    public function has(string $element): bool
    {
        return
            (
                (empty($this->excluding) || !\in_array($element, $this->excluding, true))
                &&
                (empty($this->including) || \in_array($element, $this->including, true))
            );
    }
}