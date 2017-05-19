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

$uid = $ca->getUserID();

$domainId = $_POST['domainId'];

$data = [
    'id' => addslashes($_POST['id']),
    'uid' => $uid,
    'domainId' => $domainId,
];

$domain = Capsule::table('tbldomains')
    ->where('id', '=', $domainId)->pluck('domain');

$webnic = new \liumapp\dns\models\webnic();

$webnic->initData($data);

$lmdns = \liumapp\dns\models\lmdns::findOne($data);

if($lmdns->delRecord()) {

    $lmdns->ReloadIpIndex($uid , $domainId , $lmdns->type);

    $webnic->updateDNS();

    echo 'success';

}
