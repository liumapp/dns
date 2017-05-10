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

$queryBuilder = $conn->createQueryBuilder();

$queryBuilder
    ->insert('lmdns')
    ->values(
        array(
            'uid' => '?',
            'domainId' => '?',
            'type' => '?',
            'subdomain' => '?',
            'value' => '?',
        )
    )
    ->setParameter(0, addslashes($_POST['uid']))
    ->setParameter(1, addslashes($_POST['domainId']))
    ->setParameter(2, addslashes($_POST['type']))
    ->setParameter(3, addslashes($_POST['subdomain']))
    ->setParameter(4, addslashes($_POST['value']))
;
$queryBuilder->execute();