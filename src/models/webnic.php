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

class webnic
{

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
            if (isset($this->{$key})) {
                $this->{$key} = $value;
            }
        }
    }

    public function generateAccountInfo()
    {
        $config = new \lmConfig();
        $this->source = $config->source;
        $this->password = $config->api;
    }

    public function registerRecord($isBase = false)
    {
        $this->generateAccountInfo();
        $this->otime = date('Y-m-d H:m:s', time());
        $this->generateOchecksum();
        switch ($this->type) {
            case 'A':
                if ($isBase) {
                    $result = $this->registerBaseRecord();
                } else {
                    $result = $this->registerARecord();
                }
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

    /**
     * @param bool $isBase
     * @throws \ErrorException
     * once you delete one item , you must re-built the ipIndex
     */
    public function delete($isBase = false)
    {
        $this->generateAccountInfo();
        $this->otime = date('Y-m-d H:m:s', time());
        $this->generateOchecksum();
        switch ($this->type) {
            case 'A':
                if ($isBase) {
                    $result = $this->deleteABase();
                } else {
                    $result = $this->deleteA();
                }
                break;
            case 'CNAME':
                $result = $this->deleteCNAME();
                break;
            case 'MX':
                $result = $this->deleteMX();
                break;
            case 'SPF':
                $result = $this->deleteSPF();
                break;
            default:
                throw new \ErrorException('plz input your type');
                break;
        }

        if (($info = $this->translateResult($result)) == 'success') {
            $this->isSuccess = true;
        } else {
            throw new \ErrorException($info);
        }

    }

    public function deleteABase()
    {
        throw new \ErrorException('根域名的解析记录只允许修改，不允许删除');
    }

    public function deleteA()
    {
        
        return 1;
    }

    public function deleteCNAME()
    {
        return 1;
    }

    public function deleteMX()
    {
        return 1;
    }

    public function deleteSPF()
    {
        return 1;
    }

    public function update ($isBase = false)
    {
        $this->generateAccountInfo();
        $this->otime = date('Y-m-d H:m:s' , time());
        $this->generateOchecksum();
        switch ($this->type) {
            case 'A':
                if ($isBase) {
                    $result = $this->updateABase();
                } else {
                    $result = $this->updateA();
                }

                break;
            case 'CNAME':
                $result = $this->updateCNAME();
                break;
            case 'MX':
                $result = $this->updateMX();
                break;
            case 'SPF':
                $result = $this->updateSPF();
                break;
            default:
                $result = $this->updateA();
                break;
        }
        if (($info = $this->translateResult($result)) == 'success') {
            $this->isSuccess = true;
        } else {
            throw new \ErrorException($info);
        }

    }

    public function webnicData (array $data)
    {
        $result = array(
            'encoding' => 'utf-8',
            'source' => $this->source,
            'otime' => $this->otime,
            'ochecksum' => $this->ochecksum,
            'domain' => $this->domain,
            'action' => $this->aAction,
        );

        foreach ($data as $key => $val) {
            if ($val['ipIndex'] == 1) {
                $result['sub1'] = '';
                $result['ip1'] = $val['value'];
            } else {
                $result['sub' . $val['ipIndex']] = $val['subdomain'];
                $result['ip' . $val['ipIndex']] = $val['value'];
            }

        }

        return $result;
    }

    public function updateABase ()
    {
        $lmdns = new lmdns();
        // 获取并封装旧值
        $data = $lmdns->getData(['uid' => $this->uid , 'domainId' => $this->domainId , 'type' => 'A']);
        $data = $this->webnicData($data);
        // 更新新值
        $data['sub1'] = '';
        $data['ip1'] = $this->value;
        //访问API
        $ch = curl_init();
        $url = $this->serverUrl;
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

    public function updateA ()
    {
        $lmdns = new lmdns();
        // 获取并封装旧值
        $data = $lmdns->getData(['uid' => $this->uid , 'domainId' => $this->domainId , 'type' => 'A']);
        $data = $this->webnicData($data);
        // 更新新值
        $ch = curl_init();
        $url = $this->serverUrl;
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

    public function updateCNAME ()
    {

    }

    public function updateMX ()
    {

    }

    public function updateSPF ()
    {

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

    public function registerBaseRecord ()
    {
        $ch = curl_init();
        $url = $this->serverUrl;
        $data = [
            'encoding' => 'utf-8',
            'source' => $this->source,
            'otime'  => $this->otime,
            'ochecksum' => $this->ochecksum,
            'domain' => $this->domain,
            'action' => $this->aAction,
            'ip1' => $this->value,
            'sub1' => '',
        ];
        $queryString = $this->webnic_params($data);
        curl_setopt($ch , CURLOPT_URL , $url);
        curl_setopt($ch , CURLOPT_RETURNTRANSFER , 1);
        curl_setopt($ch , CURLOPT_SSL_VERIFYHOST , FALSE);
        curl_setopt($ch , CURLOPT_SSL_VERIFYPEER , FALSE);
        curl_setopt($ch , CURLOPT_TIMEOUT , 50);
        curl_setopt($ch , CURLOPT_POST , 1);
        curl_setopt($ch , CURLOPT_POSTFIELDS , $queryString);

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