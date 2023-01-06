<?php

namespace we\utils;


trait TFork
{
    use TLog;

    protected $child_pids = [];

    public function doFork(callable $callback, ...$callbackArgs)
    {
        $child_pId = pcntl_fork();
        switch ($child_pId) {
            case -1:
                self::errorLog('fork_child_failed');
                exit(0);
                break;
            case 0:
                $result = call_user_func($callback, ...$callbackArgs);
                $parent_pid = posix_getppid();
                $child_pid = posix_getpid();
                self::infoLog("process:[{$child_pid}]", "parent:[{$parent_pid}]", "exit");
                exit(0);
            default:
                $this->child_pids[] = $child_pId;
                break;
        }
    }

    public function doWaitChildPIds()
    {
        foreach ($this->child_pids as $child_pid) {
            pcntl_waitpid($child_pid, $status);
            self::infoLog('parent_process', "child:[{$child_pid}]", "exit:[{$status}]");
        }

        self::infoLog('wait_pid', $this->child_pids);
        $this->child_pids = [];
    }
}