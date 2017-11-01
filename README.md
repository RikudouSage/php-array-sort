# PHP ArraySort

Simple class for sorting arrays, it's just a wrapper around built-in php functions.

All sorting methods are performed on a copy of array, they don't modify the original array
and return arrays. Or throw `\rikudou\ArraySortException`.

## Usage

For a better control the sorting methods are chained by the type.

Example:

```php
<?php
use rikudou\ArraySort;

$array = [
  1, 2, 3, 4, 5
];

$sorter = new ArraySort($array);

$sortedArray = $sorter->byValue()->maintainKeys()->sortReverse();
```

All methods are chained this way.

**Types:**
- `byValue()`
    - `maintainKeys()`
    - `discardKeys()`
- `byKey()`

## Magic methods

This class can also be used with magic methods, so you don't have to write the
whole chain.

**Example:**

```php
<?php
use rikudou\ArraySort;

$array = [
  1, 2, 3, 4, 5
];

$sorter = new ArraySort($array);

$sortedArray = $sorter->sortReverse();
```

The behavior is controlled by the precedence of classes, which by default is:

1. Sort by value
    1. Maintain key
    2. Discard key
2. Sort by key

The order can be changed by using static method `setOrder()` with the help of
`ArraySortPrecedenceConstants` class.

**Example:**

```php
<?php

use rikudou\ArraySort;
use rikudou\ArraySortPrecedenceConstants;

ArraySort::setOrder([
  ArraySortPrecedenceConstants::BY_KEY
]);

```

This sets that magic methods will by default be looked up in the by key sorting
methods.

You don't have to specify all of the order, the rest will be appended automatically
in default order.

So the above translates to this:

1. Sort by key
2. Sort by value
    1. Maintain key
    2. Discard key
    
**Another example:**

```php
<?php

use rikudou\ArraySort;
use rikudou\ArraySortPrecedenceConstants;

ArraySort::setOrder([
  ArraySortPrecedenceConstants::DISCARD_KEY
]);
```

This means:

1. Sort by value
    1. Discard key
    2. Maintain key
2. Sort by key

Of course you can also set the whole order:

```php
<?php

use rikudou\ArraySort;
use rikudou\ArraySortPrecedenceConstants;

ArraySort::setOrder([
  ArraySortPrecedenceConstants::BY_KEY,
  ArraySortPrecedenceConstants::BY_VALUE,
  ArraySortPrecedenceConstants::DISCARD_KEY,
  ArraySortPrecedenceConstants::MAINTAIN_KEY
]);
```

This translates to:

1. Sort by key
2. Sort by value
    1. Discard key
    2. Maintain key
    
The magic methods use reflection so it's kind of slow compared to normal chaining,
but it also uses a cache, so if it finds a method in a class once, further on it uses
the class without looking it up.

If you change the order, the cache is flushed and all magic method calls have to be
looked up again.

All magic methods have a docblock comment so IDE should be able to hint the method names.

