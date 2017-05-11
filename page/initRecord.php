<?php
/**
 * Created by PhpStorm.
 * User: liumapp
 * Email: liumapp.com@gmail.com
 * homePage: http://www.liumapp.com
 * Date: 5/11/17
 * Time: 9:20 AM
 */

require_once '../vendor/autoload.php';

require_once '../load.php';

$conn = \liumapp\dns\models\db::getInstance();
$queryBuilder = $conn->createQueryBuilder();

$uid = 1;
$domainId = 1;
$queryBuilder
    ->select('type', 'subdomain','value')
    ->from('lmdns')
    ->where('uid = ? and domainId = ?')
    ->setParameter(0, $uid)
    ->setParameter(1, $domainId)
;
$result = $queryBuilder->execute();
$rows = $result->fetchAll();
echo json_encode($rows);