<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitf4cf1be0f29399305e3931751901812d
{
    public static $prefixLengthsPsr4 = array (
        'M' => 
        array (
            'MonkeyLearn\\' => 12,
        ),
        'C' => 
        array (
            'Composer\\Installers\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'MonkeyLearn\\' => 
        array (
            0 => __DIR__ . '/..' . '/monkeylearn/monkeylearn-php/src',
        ),
        'Composer\\Installers\\' => 
        array (
            0 => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers',
        ),
    );

    public static $classMap = array (
        'Codebird\\Codebird' => __DIR__ . '/..' . '/jublonet/codebird-php/src/codebird.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitf4cf1be0f29399305e3931751901812d::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitf4cf1be0f29399305e3931751901812d::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitf4cf1be0f29399305e3931751901812d::$classMap;

        }, null, ClassLoader::class);
    }
}
