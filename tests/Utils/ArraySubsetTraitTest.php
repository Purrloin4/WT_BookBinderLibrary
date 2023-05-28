<?php

namespace App\Tests\Utils;

use PHPUnit\Framework\TestCase;

class ArraySubsetTraitTest extends TestCase
{
    use ArraySubsetTrait;

    public function testTrueIfNeedleIsEmpty(): void
    {
        $haystack = [[1, 2, [3]], 4, 5];
        $needle = [];
        $this->assertTrue($this->isArraySubset($haystack, $needle));
    }

    public function testPlainSubset(): void
    {
        $haystack = [1, 2, 3, 4, 5];
        $this->assertTrue($this->isArraySubset($haystack, [1]));
        $this->assertTrue($this->isArraySubset($haystack, [5, 2]));
        $this->assertTrue($this->isArraySubset($haystack, [4, 3, 2, 1, 5]));

        $haystack = [1, [2, 3], 4, 5];
        $this->assertTrue($this->isArraySubset($haystack, [4, 5, 1]));
        $this->assertTrue($this->isArraySubset($haystack, [2, 3]));
        $this->assertTrue($this->isArraySubset($haystack, [3, 2]));
        $this->assertTrue($this->isArraySubset($haystack, [[2, 3]]));

        // if the sub array is not sorted, it does not work.
        $this->assertFalse($this->isArraySubset($haystack, [[3, 2]]));
    }
}
