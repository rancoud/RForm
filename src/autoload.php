<?php

function autoloadRForm($class) {
    $basefile = strtolower(preg_replace('/(.)([A-Z])/', '${1}_${2}', $class));
    $classFile = $basefile . '.class.php';
    $interfaceFile = $basefile . '.class.php';

    $dir = dirname(__FILE__) . DIRECTORY_SEPARATOR;
    if(file_exists($dir.$classFile)) {
        require $dir.$classFile;
        return true;
    }

    if(file_exists($dir.$interfaceFile)) {
        require $dir.$interfaceFile;
        return true;
    }
}

spl_autoload_register('autoloadRForm');