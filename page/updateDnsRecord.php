<?php
/**
 * Created by PhpStorm.
 * User: liumapp
 * Email: liumapp.com@gmail.com
 * homePage: http://www.liumapp.com
 * Date: 5/11/17
 * Time: 2:49 PM
 */

require_once '../vendor/autoload.php';

require_once '../load.php';

$conn = \liumapp\dns\models\db::getInstance();

$queryBuilder = $conn->createQueryBuilder();

$uid = 1;

$domainId = 1;

//$queryBuilder
//    ->update('lmdns')
//    ->set('ipIndex' , '?')
//    ->where('id = ?')
//    ->setParameter(0 , 'bbba')
//    ->setParameter(1 , 9);

$queryBuilder
    ->update('lmdns')
    ->set('type', '?')
    ->set('subdomain', '?')
    ->set('value' , '?')
    ->where('id = ? and uid = ? and domainId = ?')
    ->setParameter(0, addslashes($_POST['type']))
    ->setParameter(1 , addslashes($_POST['subdomain']))
    ->setParameter(2,  addslashes($_POST['value']))
    ->setParameter(3, addslashes($_POST['id']))
    ->setParameter(4, $uid)
    ->setParameter(5,$domainId)
;
echo $queryBuilder->execute();



