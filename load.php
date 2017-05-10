<?php
/**
 * Created by PhpStorm.
 * User: liumapp
 * Email: liumapp.com@gmail.com
 * homePage: http://www.liumapp.com
 * Date: 5/10/17
 * Time: 4:53 PM
 */

function classLoadDns ($class)
{
    $path = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    $path = str_replace('liumapp' . DIRECTORY_SEPARATOR, '', $path);
    $path = str_replace('dns' . DIRECTORY_SEPARATOR , '' , $path);
    $file = __DIR__ . '/src/' . $path . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
}

spl_autoload_register('classLoadDns');