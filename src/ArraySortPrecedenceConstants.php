<?php

namespace rikudou;

class ArraySortPrecedenceConstants {

  const BY_VALUE = 1;
  const BY_KEY = 2;

  const MAINTAIN_KEY = 3;
  const DISCARD_KEY = 4;

  public static function getConstants() {
    $ref = new \ReflectionClass(static::class);
    return $ref->getConstants();
  }

  public static function getTypeConstants() {
    return [
      "BY_VALUE" => static::BY_VALUE,
      "BY_KEY" => static::BY_KEY,
    ];
  }

  public static function getKeyTypeConstants() {
    return [
      "MAINTAIN_KEY" => static::MAINTAIN_KEY,
      "DISCARD_KEY" => static::DISCARD_KEY,
    ];
  }

}