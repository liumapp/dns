<?php
/**
 * Created by PhpStorm.
 * User: liumapp
 * Email: liumapp.com@gmail.com
 * homePage: http://www.liumapp.com
 * Date: 5/10/17
 * Time: 2:30 PM
 */
use WHMCS\ClientArea;

require_once '../vendor/autoload.php';

require_once '../load.php';

require_once __DIR__ . '/../../../../../init.php';

$ca = new ClientArea();

$conn = \liumapp\dns\models\db::getInstance();

$queryBuilder = $conn->createQueryBuilder();

$uid = $ca->getUserID();

$domainId = 1;

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
    ->setParameter(0, $uid)
    ->setParameter(1, $domainId)
    ->setParameter(2, addslashes($_POST['type']))
    ->setParameter(3, addslashes($_POST['subdomain']))
    ->setParameter(4, addslashes($_POST['value']))
;
$queryBuilder->execute();

$sql = "SELECT LAST_INSERT_ID()";

$stmt = $conn->query($sql); // Simple, but has several drawbacks

$result = $stmt->fetchColumn(0);

echo $result;//返回id
