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
        echo "<script src='vendor2/vendor/liumapp/dns/bower_components/lm-editable-table/mindmup-editabletable.js'></script>";
    }

        public function renderTable ()
    {
        echo '
<div>
    <button>＋添加解析</button>
</div>
<table id="table">
    <tr>
        <th>记录类型</th>
        <th>主机记录</th>
        <th>记录值</th>
        <th>操作</th>
    </tr>
    <tr>
        <td>A</td>
        <td>@</td>
        <td>120.76.120.111</td>
        <td><button class="btn-danger" onclick="delItem(this)">删除</button></td>
    </tr>
</table>
        ';
    }


        public function renderJs()
    {
        echo '
<script>
//$(\'#table\').editableTableWidget();
function delItem (ele) {
    console.log(ele);
};
function addItem () {
    
}
</script>
        ';
    }

}
