<?php
/**
 * Created by PhpStorm.
 * User: liumapp
 * Email: liumapp.com@gmail.com
 * homePage: http://www.liumapp.com
 * Date: 5/11/17
 * Time: 9:20 AM
 */
use WHMCS\ClientArea;

use WHMCS\Database\Capsule;

require_once '../vendor/autoload.php';

require_once '../load.php';

require_once __DIR__ . '/../../../../../init.php';

$ca = new ClientArea();

$conn = \liumapp\dns\models\db::getInstance();

$queryBuilder = $conn->createQueryBuilder();

$uid = $ca->getUserID();

$domainId = addslashes($_POST['domainId']);

$queryBuilder
    ->select('id','type', 'subdomain','value')
    ->from('lmdns')
    ->where('uid = ? and domainId = ?')
    ->setParameter(0, $uid)
    ->setParameter(1, $domainId);

$result = $queryBuilder->execute();

$rows = $result->fetchAll();

echo json_encode($rows);