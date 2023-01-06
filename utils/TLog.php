<?php

namespace we\utils;


trait TLog
{
    /**
     * 日志等级
     * @var int
     */
    public static $ShowLogLevel = LogLevel::Debug;

    /**
     * TODO 间隔性输出,控制输出频率,单位秒
     * @var int
     */
    public static $IntervalSeconds = 0;

    public static function debugLog(...$args)
    {
        self::_log(LogLevel::Debug, ...$args);
    }

    private static function _log($logLevel, ...$args)
    {
        if ($logLevel < self::$ShowLogLevel) return;

        $msg = [];

        $msg[] = date('Y-m-d H:i:s');
        $msg[] = '[' . LogLevel::toName($logLevel) . ']';

        $msg = self::concatLog(... array_merge($msg, $args));

        echo $msg . PHP_EOL;
    }

    /**
     * @param mixed ...$args
     * @return string
     */
    public static function concatLog(...$args)
    {
        $msg = [];
        foreach ($args as $arg) {
            $msg[] = self::toString($arg);
        }
        return implode("\t", $msg);
    }

    /**
     * @param $val
     * @return boolean|string
     */
    public static function toString($val)
    {
        if (is_array($val) || is_object($val)) {
            return json_encode($val, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } else if (is_bool($val)) {
            return ($val ? 'TRUE' : 'FALSE');
        } else if (is_null($val)) {
            return "NULL";
        } else {
            return $val;
        }
    }

    public static function warnLog(...$args)
    {
        self::_log(LogLevel::Warn, ...$args);
    }

    public static function errorLog(...$args)
    {
        self::_log(LogLevel::Error, ...$args);
    }

    public static function fetalLog(...$args)
    {
        self::_log(LogLevel::Fetal, ...$args);
    }

    /**
     * 间隔性消息
     * @param mixed ...$args
     */
    public static function intervalLog(...$args)
    {
        static $lastTs = 0;
        $now = time();
        if (self::$IntervalSeconds <= 0 || $now - $lastTs < self::$IntervalSeconds) {
            return;
        }
        $lastTs = $now;

        self::infoLog(...$args);
    }

    public static function infoLog(...$args)
    {
        self::_log(LogLevel::Info, ...$args);
    }
}