<?php


namespace resque\test;


use we\utils\TLog;

class job_echo implements \Resque_JobInterface
{
    use TLog;
    public function perform()
    {
        // TODO: Implement perform() method.
        self::infoLog('resque-job',$this->args);
    }
}