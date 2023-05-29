<?php

namespace App\Tests\Utils;

trait ArraySubsetTrait
{
    private static function serialize_values(array $arr): array
    {
        // `array_intersect` does not work with sub-array
        // This is a workaround.
        // https://stackoverflow.com/a/19013342/4276533
        foreach ($arr as $key => $val) {
            if (!is_array($val)) {
                continue;
            }
            $arr[$key] = serialize($val);
        }

        return $arr;
    }

    private static function isArraySubsetRecursive(array $haystack, array $needle): bool
    {
        if (in_array($needle, $haystack)) {
            return true;
        }

        try {
            if (array_intersect($needle, $haystack) == $needle) {
                return true;
            }
        } catch (\Exception) {
            $s_ndle = static::serialize_values($needle);
            $s_hstk = static::serialize_values($haystack);
            if (array_intersect($s_ndle, $s_hstk) == $s_ndle) {
                return true;
            }
        }

        foreach ($haystack as $sub) {
            if (!is_array($sub)) {
                continue;
            }
            if (static::isArraySubsetRecursive($sub, $needle)) {
                return true;
            }
        }

        return false;
    }

    public static function isArraySubset(array $haystack, array $needle): bool
    {
        if (0 == count($needle)) {
            return true;
        }

        return static::isArraySubsetRecursive($haystack, $needle);
    }
}
