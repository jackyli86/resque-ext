<?php


namespace resque\test;
use resque\ext\ResqueDeamon;
use resque\ext\ResqueServer;
use resque\ext\ResqueServerConfig;
use we\utils\TCommand;
use we\utils\TLog;

require_once __DIR__ . '/../vendor/autoload.php';

class test_resque_deamon
{
    use TLog;
    use TCommand;

    public static function run(){
        $cmd = self::getCommand('--cmd', 'startup');

        // initialize resque deamon config
        $configs = [];
        $configs[] = new ResqueServerConfig(true, ['test'],1, 'localhost');
        $configs[] = new ResqueServerConfig(false, ['test'],1, 'localhost');

        // start/stop/restart
        $classname = ResqueDeamon::class;
        if (method_exists($classname, $cmd)){
            call_user_func([$classname, $cmd], $configs);
        }
        else{
            self::errorLog('unknown cmd', $cmd);
        }
    }
}

test_resque_deamon::regCommand('--cmd','[startup|shutdown|restart] default cmd is restart');
test_resque_deamon::run();
