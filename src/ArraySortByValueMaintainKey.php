<?php

namespace rikudou;

class ArraySortByValueMaintainKey {

  protected $array;

  public function __construct(array $array) {
    $this->array = $array;
  }

  /**
   * Equivalent to asort() function
   * @param int $sortFlags
   * @return array
   * @throws \rikudou\ArraySortException
   */
  public function sort(int $sortFlags = SORT_REGULAR) {
    $array = $this->array;
    if (!asort($array, $sortFlags)) {
      throw new ArraySortException("Could not sort array.");
    }
    return $array;
  }

  /**
   * Equivalent to arsort() function
   * @param int $sortFlags
   * @return array
   * @throws \rikudou\ArraySortException
   */
  public function sortReverse(int $sortFlags = SORT_REGULAR) {
    $array = $this->array;
    if (!arsort($array, $sortFlags)) {
      throw new ArraySortException("Could not sort array.");
    }
    return $array;
  }

  /**
   * Equivalent to natcasesort() function
   * @return array
   * @throws \rikudou\ArraySortException
   */
  public function naturalSortCaseInsensitive() {
    $array = $this->array;
    if (!natcasesort($array)) {
      throw new ArraySortException("Could not sort array.");
    }
    return $array;
  }

  /**
   * Equivalent to natsort() function
   * @return array
   * @throws \rikudou\ArraySortException
   */
  public function naturalSort() {
    $array = $this->array;
    if (!natsort($array)) {
      throw new ArraySortException("Could not sort array.");
    }
    return $array;
  }

  /**
   * Equivalent to uasort() function
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
    if (!uasort($array, $compareFunction)) {
      throw new ArraySortException("Could not sort array.");
    }
    return $array;
  }

}