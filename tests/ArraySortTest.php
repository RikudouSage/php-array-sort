<?php

namespace rikudou\tests;

use PHPUnit\Framework\TestCase;
use rikudou\ArraySort;
use rikudou\ArraySortByKey;
use rikudou\ArraySortByValue;
use rikudou\ArraySortByValueDiscardKey;
use rikudou\ArraySortByValueMaintainKey;
use rikudou\ArraySortException;
use rikudou\ArraySortPrecedenceConstants;

class ArraySortTest extends TestCase {

  protected $testArrayKeys = [
    "a" => 1,
    "z" => 2,
    "f" => 3,
    "x" => 4,
    "c" => 5,
    "g" => 6,
    "y" => 7,
    "test1" => 8,
    "test10" => 9,
    "test5" => 10,
  ];

  protected $testArrayValues = [
    1 => "a",
    2 => "z",
    3 => "f",
    4 => "x",
    5 => "c",
    6 => "g",
    7 => "y",
    8 => "test1",
    9 => "test10",
    10 => "test5",
  ];

  public function testCorrectClasses() {
    $sorter = new ArraySort([]);
    $this->assertInstanceOf(
      ArraySortByValue::class,
      $sorter->byValue()
    );
    $this->assertInstanceOf(
      ArraySortByKey::class,
      $sorter->byKey()
    );
    $this->assertInstanceOf(
      ArraySortByValueMaintainKey::class,
      $sorter->byValue()->maintainKeys()
    );
    $this->assertInstanceOf(
      ArraySortByValueDiscardKey::class,
      $sorter->byValue()->discardKey()
    );
  }

  public function testByKeySorts() {
    $sorter = (new ArraySort($this->testArrayKeys))->byKey();

    $copy = $this->testArrayKeys;
    ksort($copy);
    $this->assertEquals($sorter->sort(), $copy);

    krsort($copy);
    $this->assertEquals($sorter->sortReverse(), $copy);

    $cmpFunction = function ($a, $b) {
      if ($a == $b) {
        return 0;
      }
      return $a < $b ? -1 : 1;
    };

    uksort($copy, $cmpFunction);
    $this->assertEquals($sorter->customSort($cmpFunction), $copy);

  }

  public function testByValueSortsMaintainKeys() {
    $sorter = (new ArraySort($this->testArrayValues))->byValue()
      ->maintainKeys();

    $cmpFunction = function ($a, $b) {
      if ($a == $b) {
        return 0;
      }
      return $a < $b ? -1 : 1;
    };

    $copy = $this->testArrayValues;
    asort($copy);
    $this->assertEquals($sorter->sort(), $copy);
    arsort($copy);
    $this->assertEquals($sorter->sortReverse(), $copy);
    natcasesort($copy);
    $this->assertEquals($sorter->naturalSortCaseInsensitive(), $copy);
    natsort($copy);
    $this->assertEquals($sorter->naturalSort(), $copy);
    uasort($copy, $cmpFunction);
    $this->assertEquals($sorter->customSort($cmpFunction), $copy);

  }

  public function testByValueSortsDiscardKey() {
    $sorter = (new ArraySort($this->testArrayValues))->byValue()
      ->discardKey();

    $cmpFunction = function ($a, $b) {
      if ($a == $b) {
        return 0;
      }
      return $a < $b ? -1 : 1;
    };

    $copy = $this->testArrayValues;
    sort($copy);
    $this->assertEquals($sorter->sort(), $copy);
    rsort($copy);
    $this->assertEquals($sorter->sortReverse(), $copy);
    usort($copy, $cmpFunction);
    $this->assertEquals($sorter->customSort($cmpFunction), $copy);
  }

  public function testMagicMethods() {
    $sorter = new ArraySort($this->testArrayValues);
    $sorterValue = $sorter->byValue();
    $sorterValueMaintain = $sorterValue->maintainKeys();
    $sorterValueDiscard = $sorterValue->discardKey();
    $sorterKey = $sorter->byKey();

    $cmpFunction = function ($a, $b) {
      if ($a == $b) {
        return 0;
      }
      return $a < $b ? -1 : 1;
    };

    // default order

    $this->assertEquals($sorter->sort(), $sorterValue->sort());
    $this->assertEquals($sorterValue->sort(), $sorterValueMaintain->sort());

    $this->assertEquals($sorter->sortReverse(), $sorterValue->sortReverse());
    $this->assertEquals($sorterValue->sortReverse(), $sorterValueMaintain->sortReverse());

    $this->assertEquals($sorter->naturalSort(), $sorterValue->naturalSort());
    $this->assertEquals($sorterValue->naturalSort(), $sorterValueMaintain->naturalSort());

    $this->assertEquals($sorter->naturalSortCaseInsensitive(), $sorterValue->naturalSortCaseInsensitive());
    $this->assertEquals($sorterValue->naturalSortCaseInsensitive(), $sorterValueMaintain->naturalSortCaseInsensitive());

    $this->assertEquals($sorter->customSort($cmpFunction), $sorterValue->customSort($cmpFunction));
    $this->assertEquals($sorterValue->customSort($cmpFunction), $sorterValueMaintain->customSort($cmpFunction));

    try {
      $sorter->random();
      $sorter->shuffle();
    } catch (\Throwable $exception) {
      $this->fail("Random or shuffle method not found");
    }

    // keys first

    ArraySort::setOrder([
      ArraySortPrecedenceConstants::BY_KEY
    ]);

    $this->assertEquals(array_keys($sorter->sort()), array_keys($sorterKey->sort()));

    $this->assertEquals(array_keys($sorter->sortReverse()), array_keys($sorterKey->sortReverse()));

    $this->assertEquals($sorter->naturalSort(), $sorterValue->naturalSort());

    $this->assertEquals($sorter->naturalSortCaseInsensitive(), $sorterValue->naturalSortCaseInsensitive());

    $this->assertEquals(array_keys($sorter->customSort($cmpFunction)), array_keys($sorterKey->customSort($cmpFunction)));

    try {
      $sorter->random();
      $sorter->shuffle();
    } catch (\Throwable $exception) {
      $this->fail("Random or shuffle method not found");
    }

    // values first, discard key

    ArraySort::setOrder([
      ArraySortPrecedenceConstants::DISCARD_KEY
    ]);

    $this->assertEquals($sorter->sort(), $sorterValue->sort());
    $this->assertEquals($sorterValue->sort(), $sorterValueDiscard->sort());

    $this->assertEquals($sorter->sortReverse(), $sorterValue->sortReverse());
    $this->assertEquals($sorterValue->sortReverse(), $sorterValueDiscard->sortReverse());

    $this->assertEquals($sorter->naturalSort(), $sorterValue->naturalSort());
    $this->assertEquals($sorterValue->naturalSort(), $sorterValueMaintain->naturalSort());

    $this->assertEquals($sorter->naturalSortCaseInsensitive(), $sorterValue->naturalSortCaseInsensitive());
    $this->assertEquals($sorterValue->naturalSortCaseInsensitive(), $sorterValueMaintain->naturalSortCaseInsensitive());

    $this->assertEquals($sorter->customSort($cmpFunction), $sorterValue->customSort($cmpFunction));
    $this->assertEquals($sorterValue->customSort($cmpFunction), $sorterValueDiscard->customSort($cmpFunction));

    try {
      $sorter->random();
      $sorter->shuffle();
    } catch (\Throwable $exception) {
      $this->fail("Random or shuffle method not found");
    }

  }

}