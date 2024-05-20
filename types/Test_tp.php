<?php

namespace letter\types;
use letter\includes\interfaceType;

class Test_tp implements interfaceType
{
    const SHOW_NAME = "Тестовый";
    const T_VERSION = 1;
    var $a = 0;

    public static function abc(){
        return 1;
    }
}