<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit6dfc44728cf43403f4861ac89d0eccec
{
    public static $prefixLengthsPsr4 = array (
        'W' => 
        array (
            'Workerman\\' => 10,
        ),
        'G' => 
        array (
            'GatewayWorker\\' => 14,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Workerman\\' => 
        array (
            0 => __DIR__ . '/..' . '/workerman/workerman',
        ),
        'GatewayWorker\\' => 
        array (
            0 => __DIR__ . '/..' . '/workerman/gateway-worker/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit6dfc44728cf43403f4861ac89d0eccec::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit6dfc44728cf43403f4861ac89d0eccec::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
