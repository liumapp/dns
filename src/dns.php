<?php
/**
 * Created by PhpStorm.
 * User: liumapp
 * Email: liumapp.com@gmail.com
 * homePage: http://www.liumapp.com
 * Date: 5/9/17
 * Time: 9:05 AM
 */

namespace liumapp\dns;

class dns
{

    public $id;

    public $userId;

    public $domainId;

    public function init ()
    {
        echo "<link rel='stylesheet' href='vendor2/vendor/liumapp/dnspannel/dist/dnspannel.css'>";
        echo "<script src='vendor2/vendor/liumapp/dnspannel/dist/dnspannel.js'></script>";
    }

    public function renderTable ()
    {
        echo '
<div class="lm-dns-container">
    <div>
        <button class="lm-add-dns-record">＋添加解析</button>
    </div>
    <table class="lm-dns-table">
        <tr class="lm-title-tr">
            <th width="15"><input type="checkbox"></th>
            <th>记录类型</th>
            <th>主机记录</th>
            <th>记录值</th>
            <th>状态</th>
            <th>操作</th>
        </tr>
    </table>
</div>
        ';
    }

    public function renderJs()
    {
        echo '
<script>
    $(function (){
        $.lmParam.domainId = '.$this->domainId.';
        $.lmParam.addDnsRecordUrl = "http://118.190.133.67/whmcs/vendor2/vendor/liumapp/dns/page/addDnsRecord.php";
        $.lmParam.initDataUrl = "http://118.190.133.67/whmcs/vendor2/vendor/liumapp/dns/page/initRecord.php";
        $.lmParam.updateDnsRecordUrl = "http://118.190.133.67/whmcs/vendor2/vendor/liumapp/dns/page/updateDnsRecord.php";
        $.lmParam.deleteDnsRecordUrl = "http://118.190.133.67/whmcs/vendor2/vendor/liumapp/dns/page/deleteDnsRecord.php";
    });
</script>
        ';
    }

}
