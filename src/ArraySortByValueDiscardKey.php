<?php

namespace rikudou;

class ArraySortByValueDiscardKey {

  protected $array;

  public function __construct(array $array) {
    $this->array = $array;
  }

  /**
   * Equivalent to rsort() function
   * @param int $sortFlags
   * @return array
   * @throws \rikudou\ArraySortException
   */
  public function sortReverse(int $sortFlags = SORT_REGULAR) {
    $array = $this->array;
    if (!rsort($array, $sortFlags)) {
      throw new ArraySortException("Could not sort array.");
    }
    return $array;
  }

  /**
   * Equivalent to shuffle() function
   * @return array
   * @throws \rikudou\ArraySortException
   */
  public function random() {
    $array = $this->array;
    if (!shuffle($array)) {
      throw new ArraySortException("Could not sort array.");
    }
    return $array;
  }

  /**
   * Alias of random()
   * @return array
   */
  public function shuffle() {
    return $this->random();
  }

  /**
   * Equivalent to sort() function
   * @param int $sortFlags
   * @return array
   * @throws \rikudou\ArraySortException
   */
  public function sort(int $sortFlags = SORT_REGULAR) {
    $array = $this->array;
    if (!sort($array, $sortFlags)) {
      throw new ArraySortException("Could not sort array.");
    }
    return $array;
  }

  /**
   * Equivalent to usort() function
   * @param callable $compareFunction
   * @return array
   * @throws \rikudou\ArraySortException
   */
  public function customSort($compareFunction) {
    if (!is_callable($compareFunction)) {
      $type = gettype($compareFunction);
      throw new ArraySortException("The compare function must be callable, $type given.");
    }
    $array = $this->array;
    if (!usort($array, $compareFunction)) {
      throw new ArraySortException("Could not sort array.");
    }
    return $array;
  }

}