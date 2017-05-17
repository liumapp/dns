<?php
/**
 * Created by PhpStorm.
 * User: liumapp
 * Email: liumapp.com@gmail.com
 * homePage: http://www.liumapp.com
 * Date: 5/11/17
 * Time: 3:42 PM
 */

use WHMCS\ClientArea;

use WHMCS\Database\Capsule;

require_once '../load.php';

require_once __DIR__ . '/../../../../../init.php';

$ca = new ClientArea();

$id = addslashes($_POST['id']);

$uid = $ca->getUserID();

$domainId = addslashes($_POST['domainId']);

$lmdns = new \liumapp\dns\models\lmdns();

$lmdns->initData([
    'id' => $id,
    'uid' => $uid,
    'domainId' => $domainId,
]);

if ($lmdns->delRecord()) {
    echo 'success';
} else {
    echo 'del error';
}




