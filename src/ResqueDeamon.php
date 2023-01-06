<?php


namespace resque\ext;



use we\utils\TFork;
use we\utils\TSingleton;

class ResqueDeamon
{
    use TFork;
    use TSingleton;

    /**
     * @param ResqueServerConfig[] $configs
     */
    final public static function startup($configs){

        $instance = self::instance();
        foreach ($configs as $config){
            $instance->doFork([ResqueServer::class, 'startup'], $config);
        }

        sleep(3);

        self::infoLog('resque','deamon', 'start up ...');
    }

    /**
     * @param ResqueServerConfig[] $configs
     * @param bool $useAsync 异步关闭resque服务
     */
    final public static function shutdown($configs, $useAsync = false){

        $instance = self::instance();
        foreach ($configs as $config){
            if ($useAsync){
                $instance->doFork([ResqueServer::class, 'shutdown'], $config);
            }
            else{
                ResqueServer::shutdown($config);
            }

            usleep(500000);
        }

        if ($useAsync){
            $instance->doWaitChildPIds();
        }
        else{
            sleep(5);
        }

        self::infoLog('resque','deamon', 'shut down ...');
    }

    /**
     * @param ResqueServerConfig[] $configs
     */
    final public static function restartup($configs){
        self::shutdown($configs);

        self::startup($configs);
    }
}

ResqueDeamon::instance();