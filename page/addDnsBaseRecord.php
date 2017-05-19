<?php

/**
 * Created by PhpStorm.
 * User: liumapp
 * Email: liumapp.com@gmail.com
 * homePage: http://www.liumapp.com
 * Date: 5/17/17
 * Time: 2:42 PM
 */

use WHMCS\ClientArea;

use WHMCS\Database\Capsule;

require_once '../load.php';

require_once __DIR__ . '/../../../../../init.php';

$ca = new ClientArea();

$uid = $ca->getUserID();

$domainId = $_POST['domainId'];

$data = [
    'uid' => $uid,
    'domainId' => $domainId,
    'type' => addslashes($_POST['type']),
    'subdomain' => addslashes($_POST['subdomain']),
    'value' => addslashes($_POST['value']),
];

$domain = Capsule::table('tbldomains')
    ->where('id', '=', $domainId)->pluck('domain');

$webnic = new \liumapp\dns\models\webnic();

$lmdns = new \liumapp\dns\models\lmdns();

$webnic->initData($data);

$lmdns->initData($data);

$index = '1'; // for base Record

$lmdns->initData(['ipIndex' => $index]);

if ($lmdns->addRecord()) {

    $webnic->updateDNS();

    echo $lmdns->getNewRecordId();

} else {

    throw new ErrorException('save to database error');

}



