<?php
/**
 * Created by PhpStorm.
 * User: liumapp
 * Email: liumapp.com@gmail.com
 * homePage: http://www.liumapp.com
 * Date: 5/10/17
 * Time: 2:30 PM
 */

require_once '../vendor/autoload.php';

require_once '../load.php';

$conn = \liumapp\dns\models\db::getInstance();

var_dump($conn);die;


