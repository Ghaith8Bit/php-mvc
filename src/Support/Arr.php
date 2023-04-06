<?php

namespace Core\Support;

use ArrayAccess;

class Arr
{
    /**
     * Get a subset of the items from the given array.
     *
     * @param  array  $array
     * @param  array|string  $keys
     * @return array
     */
    public static function only(array $arr, array|string $keys): array
    {
        return array_intersect_key($arr, array_flip((array) $keys));
    }


    /**
     * Get all of the items in the array except for those with the specified keys.
     *
     * @param  array  $array
     * @param  array|string  $keys
     * @return array
     */
    public static function except(array $arr, array|string $keys): array
    {
        self::forget($arr, (array)$keys);
        return $arr;
    }


    /**
     * Get an item from an array using "dot" notation.
     *
     * @param  array  $array
     * @param  string $key
     * @param  mixed  $default
     * @return mixed
     */
    public static function get(array $arr, string $key, $default = null)
    {
        if (is_null($key))
            return $arr;
        $segments = explode('.', $key);
        while (count($segments) > 1) {
            $segment = array_shift($segments);
            if (self::accessible($arr) && self::exists($arr, $segment))
                $arr = $arr[$segment];
            else
                return value($default);
        }
        $segment = array_shift($segments);
        if (self::accessible($arr) && self::exists($arr, $segment))
            return $arr[$segment];
        return value($default);
    }


    /**
     * Set an item on an array using "dot" notation.
     *
     * @param  array   $array
     * @param  string  $key
     * @param  mixed   $value
     * @return bool
     */
    public static function set(array &$arr, string $key, $value): bool
    {
        if (is_null($key))
            return false;
        $segments = explode('.', $key);

        while (count($segments) > 1) {
            $segment = array_shift($segments);
            if (self::accessible($arr))
                $arr = &$arr[$segment];
            else {
                $arr === null ? $arr =  [$segment => []] : $arr =  [$arr, $segment => []];
                $arr = &$arr[$segment];
            }
        }
        if (self::accessible($arr)) {
            $key = array_shift($segments);
            $arr[$key] = $value;
        } elseif ($arr === null) {
            $key = array_shift($segments);
            $arr = [$key => $value];
        } else {
            $key = array_shift($segments);
            $arr = [$arr, $key => $value];
        }

        return true;
    }


    /**
     * Determine whether the given value is array accessible.
     *
     * @param  mixed  $value
     * @return bool
     */
    public static function accessible($value): bool
    {
        return is_array($value) || $value instanceof ArrayAccess;
    }

    /**
     * Check if an item exists in an array.
     *
     * @param  array   $array
     * @param  string  $key
     * @return bool
     */
    public static function exists(array $array, string $key): bool
    {
        return $array instanceof ArrayAccess ? $array->offsetExists($key) : isset($array[$key]);
    }


    /**
     * Check if the given key or keys exist in the provided array using "dot" notation.
     *
     * @param  array  $array
     * @param  array|string  $keys
     * @return bool
     */
    public static function has(array $arr, array|string $keys): bool
    {
        foreach ((array) $keys as $key) {
            $subArr = $arr;
            $segments = explode('.', $key);
            if (self::exists($subArr, $key))
                continue;
            foreach ($segments as $segment) {
                if (self::accessible($subArr) && self::exists($subArr, $segment)) {
                    $subArr = $subArr[$segment];
                    continue;
                }
                return false;
            }
        }
        return true;
    }


    /**
     * Remove one or many items from the given array using "dot" notation.
     *
     * @param  array   $array
     * @param  array|string  $keys
     * @return bool
     */
    public static function forget(array &$arr, array|string $keys): bool
    {
        if (!count((array) $keys)) {
            return false;
        }
        foreach ((array) $keys as $key) {
            if (self::exists($arr, $key)) {
                unset($arr[$key]);
                continue;
            }
            $segments = explode('.', $key);
            $subArr = &$arr;
            while (count($segments) > 1) {
                $segment = array_shift($segments);
                if (self::exists($subArr, $segment) && self::accessible($subArr)) {
                    $subArr = &$subArr[$segment];
                } else {
                    return false;
                }
            }
            unset($subArr[array_shift($segments)]);
        }
        return true;
    }
}
