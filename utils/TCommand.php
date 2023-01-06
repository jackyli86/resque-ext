<?php

namespace we\utils;


trait TCommand
{
    /**
     * 控制台输入的有效命令 hash
     * @var null | array
     */
    protected static $input_commands = null;
    /**
     * 已注册的命令
     * @var array
     */
    protected static $reg_commands = array();

    public static function isCliMode()
    {
        return php_sapi_name() == 'cli';
    }

    /**
     * todo $cmd_name 的规则
     * 1. 必须以 ‘-’ 开头
     * 2. 如果第二个字符不是 ‘-’ 则设置了该字段 则val为true
     * 3. 否则 则 需要输入 相应的 val
     * @param $cmd_name
     * @param $cmd_tip
     * @throws Exception
     */
    final public static function regCommand($cmd_name, $cmd_tip)
    {
        self::$reg_commands[$cmd_name] = self::check_build_command($cmd_name, $cmd_tip);
    }

    /**
     * @param $cmd_name
     * @param $cmd_tip
     * @param array $cmd_alias
     * @return array
     * @throws Exception
     */
    private static function check_build_command($cmd_name, $cmd_tip, $cmd_alias = [])
    {
        $cmd_name_len = strlen($cmd_name);
        if ($cmd_name_len < 2) throw new Exception('cmd_name must 2 char at least');
        if ($cmd_name[0] != '-') throw new Exception('cmd_name[0] must equal to "-"');

        if ($cmd_name[1] == '-' && $cmd_name_len > 2) $cmd_input = true;
        else $cmd_input = false;

        return array(
            'cmd_name' => $cmd_name,
            'cmd_tip' => $cmd_tip,
            'cmd_input' => $cmd_input,
        );
    }

    final public static function getCommandAsInt($name, $default = 0): int
    {
        self::parse_command();
        return intval(self::getCommand($name, $default));
    }

    private static function parse_command()
    {
        if (self::$input_commands !== null) {
            return self::$input_commands;
        }

        self::$input_commands = array();

        global $argc, $argv;
        for ($i = 1; $i < $argc; ++$i) {
            $cmd_name = $argv[$i];
            if (!isset(self::$reg_commands[$cmd_name])) {
                die(TLog::concatLog('unknown command', "[{$cmd_name}]"));
            }

            $cmd = self::$reg_commands[$cmd_name];
            if ($cmd['cmd_input']) {
                if ($i + 1 >= $argc) {
                    die(TLog::concatLog('missing command value', "[{$cmd_name}]"));
                }

                self::$input_commands[$cmd_name] = $argv[$i + 1];
                ++$i;
            } else if (!$cmd['cmd_input']) {
                self::$input_commands[$cmd_name] = true;
            }
        }

        return self::$input_commands;
    }

    /**
     * @param $name
     * @param $default
     * @return null|true|string
     * null 表示没有输入该命令
     * true 表示设置了该字段并且该字段为标记字段
     * val  表示 以 ‘--’ 开头的字段的 值
     */
    final public static function getCommand($name, $default = null)
    {
        self::parse_command();
        return self::$input_commands[$name] ?? $default;
    }
}