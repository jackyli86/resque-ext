<?php

namespace we\utils;

class LogLevel
{
    use TConstReflection;

    const Debug = 1;
    const Info = 2;
    const Warn = 3;
    const Error = 4;
    const Fetal = 5;
}

LogLevel::doReflection();
