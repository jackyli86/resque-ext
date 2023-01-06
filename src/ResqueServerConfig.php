<?php


namespace resque\ext;


class ResqueServerConfig
{
    protected $configId = '000';

    protected $worker = false;

    protected $workerCount = 1;

    protected $queues = [];

    protected $redisBackEnd = null;

    /**
     * ResqueServerConfig constructor.
     * @param bool $isWorker
     * @param array $queues
     * @param int $workerCount
     * @param string $redisBackEnd
     */
    public function __construct($isWorker, $queues = [], $workerCount = 1, $redisBackEnd = 'localhost')
    {
        $this->worker = $isWorker;
        if ($isWorker){
            static $idGenerator = 1;

            $this->configId = sprintf('%03d',$idGenerator ++);

            // 1 <= $workerCount <= 16
            $this->workerCount = max(1,min(16,$workerCount));

            $this->queues = array_unique($queues);
        }
        else{
            $this->configId = sprintf('%03d',0);
            $this->workerCount = 1;
        }

        $this->redisBackEnd = $redisBackEnd;
    }

    public function getWorkerName(){
        return explode('.',basename($this->getPIdFile()))[0];
    }

    public function getWorkerCount(){
        return $this->workerCount;
    }

    /**
     * redis 连接字符串
     * @return string
     */
    public function getRedisBackEnd() : string{
        return $this->redisBackEnd;
    }

    /**
     * 进程pid存储文件
     * @return string
     */
    public function getPIdFile() : string{
        return $this->isWorker()
            ? "/tmp/resque-worker-{$this->configId}.pid"
            : "/tmp/resque-scheduler-{$this->configId}.pid";
    }

    /**
     * 要执行的php文件 bin/resque | bin/resque-scheduler
     * @return string
     */
    public function getMainFile() : string{
        static $main_files = [];

        if (!empty($main_files)){
            return $this->isWorker()
                ? $main_files[0]
                : $main_files[1];
        }

        $files = array(
            __DIR__ . '/../vendor/autoload.php',
            __DIR__ . '/../../../autoload.php',
        );

        foreach ($files as $autoload_file){
            if (file_exists($autoload_file)){
                $autoload_dir = dirname($autoload_file);

                $main_files[] = $autoload_dir . DIRECTORY_SEPARATOR . 'bin/resque';
                $main_files[] = $autoload_dir . DIRECTORY_SEPARATOR . 'bin/resque-scheduler';

                break;
            }
        }

        return $this->isWorker()
            ? $main_files[0]
            : $main_files[1];
    }

    /**
     * 是否是 bin/resque
     * @return bool
     */
    public function isWorker() : bool{
        return $this->worker;
    }

    /**
     * bin/resque 要处理的队列 * 表示处理所有
     * @return string
     */
    public function getQueue() : string {
        return implode(',', $this->queues);
    }

}

