<?php


namespace resque\ext;

use Resque;
use ResqueScheduler;

class ResqueClient
{
    public static function enqueue($queue, $class, $args = null, $prefix = '')
    {
        return Resque::enqueue($queue, $class, $args, true, $prefix);
    }

    public static function enqueueAt($at, $queue, $class, $args = null)
    {
        ResqueScheduler::enqueueAt($at, $queue, $class, $args);
    }

    public static function enqueueIn($in, $queue, $class, $args = null)
    {
        ResqueScheduler::enqueueIn($in, $queue, $class, $args);
    }

    public static function startup()
    {
        static $initialized = false;
        if ($initialized) return;
        $initialized = true;

        // 已经初始化, 不再进行初始化了
        if (Resque::$redis !== null) {
            return;
        }

        $instance = ResqueClientConfig::instance();
        $redisBackEnd = $instance->getRedisBackEnd();
        $redisBackEndDb = $instance->getRedisBackEndDb();
        Resque::setBackend($redisBackEnd, $redisBackEndDb);
    }
}

ResqueClient::startup();