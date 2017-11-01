<?php

namespace rikudou;

/**
 * All the methods to sort array by key.
 *
 * @package rikudou
 */
class ArraySortByKey {

  protected $array;

  public function __construct(array $array) {
    $this->array = $array;
  }

  /**
   * Sorts the array in reverse by key, equivalent to krsort() function
   *
   * @param int $sortFlags
   * @return array
   * @throws \rikudou\ArraySortException
   */
  public function sortReverse(int $sortFlags = SORT_REGULAR) {
    $array = $this->array;
    if (!krsort($array, $sortFlags)) {
      throw new ArraySortException("Could not sort array.");
    }
    return $array;
  }

  /**
   * Sorts the array by key, equivalent to ksort() function
   *
   * @param int $sortFlags
   * @return array
   * @throws \rikudou\ArraySortException
   */
  public function sort(int $sortFlags = SORT_REGULAR) {
    $array = $this->array;
    if (!ksort($array, $sortFlags)) {
      throw new ArraySortException("Could not sort array.");
    }
    return $array;
  }

  /**
   * Sorts the array by key with provided sort function, equivalent to
   * uksort() function
   *
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
    if (!uksort($array, $compareFunction)) {
      throw new ArraySortException("Could not sort array.");
    }
    return $array;
  }

}