<?php


namespace resque\ext;


use we\utils\TLog;

class ResqueServer
{
    use TLog;

    const StopWaitSeconds = 8;

    public static function echoConfig(){
        $configs = [
            'REDIS_BACKEND',
            'PIDFILE',
            'LOGGING',
            'VERBOSE',
            'VVERBOSE',
            'QUEUE',
            'COUNT',
            'INTERVAL',
        ];

        self::infoLog('EnvName','EnvValue');
        foreach ($configs as $name){
            self::infoLog($name, getenv($name));
        }
    }

    public static function isProcessAlive($pidFile): bool
    {
        if (!file_exists($pidFile)) return false;

        $pid = file_get_contents($pidFile);
        exec("ps -ef | awk '{print $2}' | grep ^$pid$", $output, $result_code);
        return !empty($output);
    }


    public static function startup(ResqueServerConfig $resqueServerConfig){

        $redisBackEnd = $resqueServerConfig->getRedisBackEnd();
        $mainFile = $resqueServerConfig->getMainFile();
        $pidFile = $resqueServerConfig->getPIdFile();
        $queue = $resqueServerConfig->getQueue();
        $isWorker = $resqueServerConfig->isWorker();

        $workerName = $resqueServerConfig->getWorkerName();
        $workerCount = $resqueServerConfig->getWorkerCount();

        $isAlive = self::isProcessAlive($pidFile);
        if ($isAlive){
            self::infoLog($workerName . ' is running');
            exit(0);
        }

        \Resque_Event::listen('beforeFirstFork', function($worker)use($workerName,$pidFile){
            self::infoLog($workerName,'startup', self::isProcessAlive($pidFile) ? 'success' : 'fail');
        });

        putenv("REDIS_BACKEND=$redisBackEnd");
        putenv("PIDFILE=$pidFile");

        // putenv("LOGGING=1");
        if ($isWorker){
            putenv("QUEUE=$queue");
            putenv("COUNT=$workerCount");

            putenv("INTERVAL=1");
        }

        self::infoLog($workerName, __FUNCTION__,'...');
        require $mainFile;
    }

    public static function shutdown(ResqueServerConfig $resqueConfig, $useAsync = false){
        $redisBackEnd = $resqueConfig->getRedisBackEnd();
        $mainFile = $resqueConfig->getMainFile();
        $pidFile = $resqueConfig->getPIdFile();
        $queue = $resqueConfig->getQueue();
        $isWorker = $resqueConfig->isWorker();
        $workerName = $resqueConfig->getWorkerName();

        $isAlive = self::isProcessAlive($pidFile);
        if (!$isAlive) {
            self::infoLog($workerName . ' has stopped');
            if ($useAsync){
                exit(0);
            }
            else{
                return;
            }
        }

        $process_id = file_get_contents($pidFile);
        posix_kill($process_id,SIGQUIT);

        $stopTime = time() + self::StopWaitSeconds;
        do{
            sleep(1);
        }while(self::isProcessAlive($pidFile) && $stopTime > time());

        self::infoLog($workerName, __FUNCTION__, !self::isProcessAlive($pidFile) ? 'success' : 'fail');
    }

}
