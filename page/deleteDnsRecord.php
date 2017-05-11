<?php
/**
 * Created by PhpStorm.
 * User: liumapp
 * Email: liumapp.com@gmail.com
 * homePage: http://www.liumapp.com
 * Date: 5/11/17
 * Time: 3:42 PM
 */


require_once '../vendor/autoload.php';

require_once '../load.php';

$conn = \liumapp\dns\models\db::getInstance();

$queryBuilder = $conn->createQueryBuilder();

$uid = 1;

$domainId = 1;

$queryBuilder->delete('lmdns')
    ->where('id = :record_id')
    ->andWhere('uid = :uid')
    ->andWhere('domainId = :domainId')
    ->setParameter(':record_id', addslashes($_POST['id']))
    ->setParameter(':uid' , $uid)
    ->setParameter(':domainId' , $domainId)
    ->execute();

echo $queryBuilder->execute();



