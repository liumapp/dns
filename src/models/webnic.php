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

require_once  __DIR__ . '/../../../../../../lmConfig.php';

class webnic  {

    public $uid;

    public $domainId;

    public $type;

    public $subdomain;

    public $value;

    public $ipIndex;

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

    public $aAction = 'apointer'; //A记录解析：apointer

    public $cnameAction = 'cnrecord';

    public $mxAction = 'mxrecord';

    public $spfAction = 'spfrecord';

    protected $isSuccess = false;

    public function initData(array $data)
    {
        foreach ($data as $key => $value) {

            $this->{$key} = $value;

        }
    }

    public function generateAccountInfo ()
    {
        $config = new \lmConfig();
        $this->source = $config->source;
        $this->password = $config->api;
    }

    public function registerRecord ()
    {
        $this->generateAccountInfo();
        $this->otime = date('Y-m-d H:m:s' , time());
        $this->generateOchecksum();
        switch ($this->type) {
            case 'A':
                $result =  $this->registerARecord();
                break;
            case 'CNAME':
                $result = $this->registerCNAMERecord();
                break;
            case 'MX':
                $result = $this->registerMXRecord();
                break;
            case 'SPF':
                $result = $this->registerSPFRecord();
                break;
            default:
                $this->registerARecord();
                break;
        }
        if (($info = $this->translateResult($result)) == 'success') {
            $this->isSuccess = true;
        } else {
            throw new \ErrorException($info);
        }
    }

    public function isSuccess ()
    {
        return $this->isSuccess;
    }

    /**
     * 生成ochecksum
     */
    private function generateOchecksum ()
    {
        $this->ochecksum = md5($this->source . $this->otime . md5($this->password));
    }

    /**
     * 解析返回结果
     */
    public function translateResult ($data)
    {
        $data = substr($data , 0 , 1);
        switch ($data) {
            case 0 :
                return 'success';
            case 2 :
                return 'the domain is not yours';//不属于你的代理商，不是指用户
            case 4 :
                return 'DNS server wrong';
            case 5 :
                return 'Invalid Ip';
            case 6 :
                return 'authentication failed';
        }
    }

    private function webnic_params($postfields, $key = "") {
        $query_string = "";
        foreach ($postfields AS $k => $v) {
            if (is_array($v)) {
                $query_string.=$this->webnic_params($v, $k);
            } else {
                if ($key != "") {
                    $k = $key;
                }
                $query_string .= "$k=" . urlencode($v) . "&";
            }
            //$query_string .= "$k=".$v."&";
        }
        return $query_string;
    }

    public function registerARecord ()
    {
        $ch = curl_init();
        $url = $this->serverUrl;
        $data = array(
            'encoding' => 'utf-8',
            'source' => $this->source,
            'otime' => $this->otime,
            'ochecksum' => $this->ochecksum,
            'domain' => $this->domain,
            'action' => $this->aAction,
            'ip' . $this->ipIndex => $this->value,
            'sub' . $this->ipIndex => $this->subdomain
        );
        $queryString = $this->webnic_params($data);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER , 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_TIMEOUT, 50);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
            $queryString
        );

        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    public function registerMXRecord ()
    {
        $ch = curl_init();
        $url = $this->serverUrl;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER , 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
            http_build_query(
                array(
                    'source' => $this->source,
                    'otime' => $this->otime,
                    'ochecksum' => $this->ochecksum,
                    'domain' => $this->domain,
                    'action' => $this->mxAction,
                    'mx' . $this->ipIndex => $this->value
                )
            )
        );
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    public function registerCNAMERecord ()
    {
        $ch = curl_init();
        $url = $this->serverUrl;
        $data = [
            'source' => $this->source,
            'otime' => $this->otime,
            'ochecksum' => $this->ochecksum,
            'domain' => $this->domain,
            'action' => $this->cnameAction,
        ];
        if ($this->ipIndex == 1 ) {
            $data['c'] = $this->value;
            $data['cs'] = $this->subdomain;
        } else {
            $data['c' . $this->ipIndex] = $this->value;
            $data['cs' . $this->ipIndex] = $this->subdomain;
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER , 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
            http_build_query(
                $data
            )
        );
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    public function registerSPFRecord ()
    {
        $ch = curl_init();
        $url = $this->serverUrl;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER , 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
            http_build_query(
                array(
                    'source' => $this->source,
                    'otime' => $this->otime,
                    'ochecksum' => $this->ochecksum,
                    'domain' => $this->domain,
                    'action' => $this->spfAction,
                    'spf' . $this->ipIndex => $this->value
                )
            )
        );

        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

}