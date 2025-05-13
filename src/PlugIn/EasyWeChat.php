<?php

namespace Lsshu\Site\Api\PlugIn;

use EasyWeChat\Factory;

class EasyWeChat
{
    public static function miniProgram($config = [])
    {
        $base_config = config('site-api.mini_program', []);
        $config = array_merge($base_config, $config);
        return Factory::miniProgram($config);
    }
}
