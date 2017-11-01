<?php

namespace rikudou;

/**
 * Allows to sort arrays without modifying the original using OOP.
 *
 * You can use the sorting method by chaining:
 *  $sorter->byValue()->maintainKeys()->sort();
 *
 * Or you can use the magic __call method and call it directly:
 *  $sorter->sort();
 *
 * Since there are multiple methods named sort() (e.g. in sorting by key, by
 * value, with maintaining key and discarding key), this class implements
 * a precedence settings.
 *
 * It's used as a static method call:
 *
 *  ArraySort::setOrder([
 *    // order
 *  ]);
 *
 * For setting the order you can use the constants from
 * ArraySortPrecedenceConstants class. The default is this:
 *  [
 *    ArraySortPrecedenceConstants::BY_VALUE,
 *    ArraySortPrecedenceConstants::BY_KEY,
 *    ArraySortPrecedenceConstants::MAINTAIN_KEY,
 *    ArraySortPrecedenceConstants::DISCARD_KEY,
 *  ]
 *
 * Which means that if you call the method sort() on this class,
 * it will first search for it by value and maintaining key.
 *
 * With this precedence setting it's equivalent to:
 *  $sorter->byValue()->maintainKey()->sort();
 *
 * If you change it this way:
 *
 *  ArraySort::setOrder([
 *    ArraySortPrecedenceConstants::BY_KEY,
 *  ]);
 *
 * The call to sort() becomes equivalent to this:
 *  $sorter->byKey()->sort();
 *
 * @package rikudou
 * @method array sort(int $sortFlags = SORT_REGULAR);
 * @method array sortReverse(int $sortFlags = SORT_REGULAR);
 * @method array naturalSortCaseInsensitive();
 * @method array naturalSort();
 * @method array customSort(callable $compareFunction);
 * @method array random();
 */
class ArraySort {

  protected $array;

  protected static $order = [
    ArraySortPrecedenceConstants::BY_VALUE,
    ArraySortPrecedenceConstants::BY_KEY,
  ];

  protected static $cache = [];

  public function __construct(array $array) {
    $this->array = $array;
  }

  /**
   * Further show methods that allow you to sort by value
   * @return \rikudou\ArraySortByValue
   */
  public function byValue() {
    return new ArraySortByValue($this->array);
  }

  /**
   * Futher show methods that allow you to sort by key
   * @return \rikudou\ArraySortByKey
   */
  public function byKey() {
    return new ArraySortByKey($this->array);
  }

  /**
   * Allows you to change order for magic methods calling.
   *
   * For description see the class summary docblock.
   *
   * You don't have to set all the constants, if you miss some, it will be
   * appended automatically.
   *
   * @param array $order
   */
  public static function setOrder(array $order) {

    static::$cache = [];

    $typeConstants = ArraySortPrecedenceConstants::getTypeConstants();

    static::$order = [];
    foreach ($order as $item) {
      if (in_array($item, $typeConstants)) {
        static::$order[] = $item;
      }
    }

    foreach ($typeConstants as $constant) {
      if (!in_array($constant, static::$order)) {
        static::$order[] = $constant;
      }
    }

    ArraySortByValue::setOrder($order);
  }

  /**
   * Returns the order array
   * @return array
   */
  public static function getOrder() {
    return array_merge(static::$order, ArraySortByValue::getOrder());
  }

  /**
   * Here the magic happens, this method checks for the method name
   * in other classes according to the precedence settings.
   *
   * @param string $name
   * @param array $arguments
   * @return array
   * @throws \rikudou\ArraySortException
   */
  public function __call($name, $arguments) {
    if (isset(static::$cache[$name])) {
      $class = static::$cache[$name];
      $object = new $class($this->array);
      return call_user_func_array([$object, $name], $arguments);
    }
    $classes = [];
    foreach (static::$order as $item) {
      switch ($item) {
        case ArraySortPrecedenceConstants::BY_VALUE:
          $classes[] = ArraySortByValue::class;
          break;
        case ArraySortPrecedenceConstants::BY_KEY:
          $classes[] = ArraySortByKey::class;
          break;
      }
    }
    foreach ($classes as $class) {
      $exists = FALSE;
      if (method_exists($class, $name)) {
        $exists = TRUE;
      }
      if (!$exists) {
        $docComment = (new \ReflectionClass($class))->getDocComment();
        $docComment = explode("\n", $docComment);
        $regex = '@.*?\@method\s.+?\s(.+?)\s*\(.*@';
        $methods = [];
        foreach ($docComment as $line) {
          if (strpos($line, "@method") === FALSE) {
            continue;
          }
          if (!preg_match($regex, $line, $matches)) {
            continue;
          }
          $methods[] = $matches[1];
        }
        if (in_array($name, $methods)) {
          $exists = TRUE;
        }
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