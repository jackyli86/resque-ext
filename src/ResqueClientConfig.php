<?php


namespace resque\ext;


use we\utils\TSingleton;

class ResqueClientConfig
{
    use TSingleton;

    protected $redisBackEnd = 'localhost';

    protected $redisBackEndDb = 0;

    public function setRedisBackEnd($redisBackEnd){
        $this->redisBackEnd = $redisBackEnd;
    }

    public function getRedisBackEnd(){
        return $this->redisBackEnd;
    }

    public function setRedisBackEndDb($redisBackEndDb){
        $this->redisBackEndDb = $redisBackEndDb;
    }

    public function getRedisBackEndDb(){
        return $this->redisBackEndDb;
    }
}