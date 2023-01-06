<?php


namespace resque\test;
require_once __DIR__ . '/../vendor/autoload.php';

use resque\ext\ResqueClient;
use resque\ext\ResqueClientConfig;
use we\utils\TLog;

class test_resque_client
{
    use TLog;
    public static function run(){
        // initialize resque client config
        $instance = ResqueClientConfig::instance();
        $instance->setRedisBackEnd('localhost');
        $instance->setRedisBackEndDb(0);

        $job_id = ResqueClient::enqueue('test',job_echo::class, ['time' => date('Y-m-d H:i:s')]);

        self::infoLog($job_id);
    }
}

test_resque_client::run();
