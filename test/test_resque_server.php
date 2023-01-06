<?php


namespace resque\test;
use resque\ext\ResqueServer;
use resque\ext\ResqueServerConfig;
use we\utils\TCommand;
use we\utils\TLog;

require_once __DIR__ . '/../vendor/autoload.php';

class test_resque_server
{
    use TLog;
    use TCommand;

    public static function run(){
        $cmd = self::getCommand('--cmd', 'startup');

        // initialize resque server config
        $config = new ResqueServerConfig(self::getCommand('-worker', false), ['test'],1, 'localhost');

        // start/stop resque server
        $classname = ResqueServer::class;
        if (method_exists($classname, $cmd)){
            call_user_func([$classname, $cmd], $config);
        }
        else{
            self::errorLog('unknown cmd', $cmd);
        }
    }
}

test_resque_server::regCommand('--cmd','[startup|shutdown|restart] default cmd is restart');
test_resque_server::regCommand('-worker', 'iswoker');
test_resque_server::run();
