<?php
/**
 * Created by PhpStorm.
 * User: liumapp
 * Email: liumapp.com@gmail.com
 * homePage: http://www.liumapp.com
 * Date: 5/10/17
 * Time: 4:47 PM
 */
namespace liumapp\dns\models;

class db {

    public static function getInstance()
    {

        $config = new \Doctrine\DBAL\Configuration();
//..
        $connectionParams = array(
            'dbname' => 'whmcs',
            'user' => 'root',
            'password' => 'adminadmin',
            'host' => '118.190.133.67',
            'driver' => 'pdo_mysql',
        );

        $conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);

        return $conn;

    }

}