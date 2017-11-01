<?php

namespace rikudou;

/**
 * Class ArraySortByValue
 * @package rikudou
 * @method array sort(int $sortFlags = SORT_REGULAR);
 * @method array sortReverse(int $sortFlags = SORT_REGULAR);
 * @method array naturalSortCaseInsensitive();
 * @method array naturalSort();
 * @method array customSort(callable $compareFunction);
 * @method array random();
 * @method array shuffle();
 */
class ArraySortByValue {

  protected $array;

  protected static $order = [
    ArraySortPrecedenceConstants::MAINTAIN_KEY,
    ArraySortPrecedenceConstants::DISCARD_KEY,
  ];

  protected static $cache = [];

  public function __construct(array $array) {
    $this->array = $array;
  }

  /**
   * Returns object that contains methods to sort while maintaining key
   * @return \rikudou\ArraySortByValueMaintainKey
   */
  public function maintainKeys() {
    return new ArraySortByValueMaintainKey($this->array);
  }

  /**
   * Returns object that contains methods to sort while discarding key
   * @return \rikudou\ArraySortByValueDiscardKey
   */
  public function discardKey() {
    return new ArraySortByValueDiscardKey($this->array);
  }

  /**
   * Sets the order for this class, is called automatically from ArraySort class
   * @param array $order
   * @internal
   */
  public static function setOrder(array $order) {
    static::$cache = [];

    $keyConstants = ArraySortPrecedenceConstants::getKeyTypeConstants();

    static::$order = [];
    foreach ($order as $item) {
      if (in_array($item, $keyConstants)) {
        static::$order[] = $item;
      }
    }

    foreach ($keyConstants as $constant) {
      if (!in_array($constant, static::$order)) {
        static::$order[] = $constant;
      }
    }
  }

  /**
   * Returns the order array
   * @return array
   * @internal
   */
  public static function getOrder() {
    return static::$order;
  }

  public function __call($name, $arguments) {
    if (isset(static::$cache[$name])) {
      $class = static::$cache[$name];
      $object = new $class($this->array);
      return call_user_func_array([$object, $name], $arguments);
    }
    $classes = [];
    foreach (static::$order as $item) {
      switch ($item) {
        case ArraySortPrecedenceConstants::MAINTAIN_KEY:
          $classes[] = ArraySortByValueMaintainKey::class;
          break;
        case ArraySortPrecedenceConstants::DISCARD_KEY:
          $classes[] = ArraySortByValueDiscardKey::class;
          break;
      }
    }
    foreach ($classes as $class) {
      $exists = FALSE;
      if (method_exists($class, $name)) {
        $exists = TRUE;
      }
      if ($exists) {
        static::$cache[$name] = $class;
        $object = new $class($this->array);
        return call_user_func_array([$object, $name], $arguments);
      }
    }
    throw new ArraySortException("The method $name does not exist.");
  }

}