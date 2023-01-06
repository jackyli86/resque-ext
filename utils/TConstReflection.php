<?php

namespace we\utils;

use ReflectionClass;

trait TConstReflection
{
    // for example
    // todo const A = 1024;
    // A is Name,
    // 1024 is Value

    public static $_value2name = [];
    public static $_name2value = [];

    public static function doReflection()
    {
        static $initialized = false;
        if ($initialized) return;
        $initialized = true;

        $reflectionObj = new ReflectionClass(static::class);
        self::$_name2value = $reflectionObj->getConstants();
        foreach (self::$_name2value as $name => $value) {
            self::$_value2name[$value] = $name;
        }
    }

    public static function toName($value)
    {
        return self::$_value2name[$value] ?? $value;
    }

    public static function toValue($name)
    {
        return self::$_name2value[$name] ?? $name;
    }

    public static function existName($name): bool
    {
        return isset(self::$_name2value[$name]);
    }

    public static function existValue($value): bool
    {
        return isset(self::$_value2name[$value]);
    }

    public static function enumName2Value(callable $cb)
    {
        foreach (self::$_name2value as $name => $value) {
            call_user_func($cb, $name, $value);
        }
    }

}