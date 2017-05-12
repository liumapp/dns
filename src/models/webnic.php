<?php
/**
 * Created by PhpStorm.
 * User: liumapp
 * Email: liumapp.com@gmail.com
 * homePage: http://www.liumapp.com
 * Date: 5/12/17
 * Time: 9:51 AM
 */

namespace liumapp\dns\models;

class webnic  {

    protected $serverUrl = 'https://my.webnic.cc/jsp/pn_valuesadd.jsp';

    /**
     * @var string
     */
    public $source; //代理商账号

    public $password; // 代理商API密码

    public $otime; //购买时间，格式 YYYY-MM-DD HH:MM:SS 范例: 2000-02-20 12:03:33

    public $url; //这是到您模板的完整URL路径。 WEBNIC 服务器将会把 结果返回至此模板。

    public $ochecksum; //代理商与 WEBNIC 之间的 MD5 验证密钥。

    public $domain; //需要更新的域名

    public $action = 'apointer'; //A记录解析：apointer

    public $ip1; //第一个A值,以 IP 地址格式输入。

    public $sub1; //指向 ip1的子域名,建议将参数设置为www,否则 www.yourdomain.com 无法正常解析。(www.yourdomain.com就填写为www即可)

    public $ip2; //第一个A值,以 IP 地址格式输入。

    public $sub2; //指向 ip1的子域名,建议将参数设置为www,否则 www.yourdomain.com 无法正常解析。(www.yourdomain.com就填写为www即可)

    public $ip3; //第一个A值,以 IP 地址格式输入。

    public $sub3; //指向 ip1的子域名,建议将参数设置为www,否则 www.yourdomain.com 无法正常解析。(www.yourdomain.com就填写为www即可)

    public $ip4; //第一个A值,以 IP 地址格式输入。

    public $sub4; //指向 ip1的子域名,建议将参数设置为www,否则 www.yourdomain.com 无法正常解析。(www.yourdomain.com就填写为www即可)

    public $ip5; //第一个A值,以 IP 地址格式输入。

    public $sub5; //指向 ip1的子域名,建议将参数设置为www,否则 www.yourdomain.com 无法正常解析。(www.yourdomain.com就填写为www即可)

    public $ip6; //第一个A值,以 IP 地址格式输入。

    public $sub6; //指向 ip1的子域名,建议将参数设置为www,否则 www.yourdomain.com 无法正常解析。(www.yourdomain.com就填写为www即可)

    public $ip7; //第一个A值,以 IP 地址格式输入。

    public $sub7; //指向 ip1的子域名,建议将参数设置为www,否则 www.yourdomain.com 无法正常解析。(www.yourdomain.com就填写为www即可)

    public $ip8; //第一个A值,以 IP 地址格式输入。

    public $sub8; //指向 ip1的子域名,建议将参数设置为www,否则 www.yourdomain.com 无法正常解析。(www.yourdomain.com就填写为www即可)

    public $ip9; //第一个A值,以 IP 地址格式输入。

    public $sub9; //指向 ip1的子域名,建议将参数设置为www,否则 www.yourdomain.com 无法正常解析。(www.yourdomain.com就填写为www即可)

    public $ip10; //第一个A值,以 IP 地址格式输入。

    public $sub10; //指向 ip1的子域名,建议将参数设置为www,否则 www.yourdomain.com 无法正常解析。(www.yourdomain.com就填写为www即可)

    public function initData(array $data)
    {
        foreach ($data as $key => $value) {

            $this->{$key} = $value;

        }
    }

    public function registerRecord ()
    {

    }

    public function isSuccess ()
    {

    }

    /**
     * 生成ochecksum
     */
    private function generateOchecksum ()
    {
        $this->ochecksum = md5($this->source . $this->otime . md5($this->password));
    }

    private function generateAction ()
    {
        $this->action = [
            'A' => 'apointer',
            'MX' => 'mxrecord',
            'CNAME' => 'cnrecord',
            'SPF' => 'spfrecord'
        ];
    }

    /**
     * 解析返回结果
     */
    public function translateResult ($data)
    {
        switch ($data) {
            case 0 :
                return 'success';
            case 1 :
                return 'the domain is not yours';//不属于你的代理商，不是指用户
            case 4 :
                return 'DNS server wrong';
            case 5 :
                return 'Invalid Ip';
            case 6 :
                return 'authentication failed';
        }
    }



    public function registerARecord ()
    {

    }

    public function registerMXRecord ()
    {

    }

    public function registerCNAMERecord ()
    {

    }

    public function registerSPFRecord ()
    {

    }

}