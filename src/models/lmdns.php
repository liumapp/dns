<?php
/**
 * Created by PhpStorm.
 * User: liumapp
 * Email: liumapp.com@gmail.com
 * homePage: http://www.liumapp.com
 * Date: 5/12/17
 * Time: 3:10 PM
 */

namespace liumapp\dns\models;

use Composer\Script\PackageEvent;

require_once __DIR__ . '/../../vendor/autoload.php';

class lmdns
{

    public $id;

    public $uid;

    public $domainId;

    public $type;

    public $subdomain;

    public $value;

    public $ipIndex;

    public $tableName = 'lmdns';

    public function initData(array $data)
    {
        foreach ($data as $key => $value) {

            if (property_exists( $this , $key)) {
                $this->{$key} = $value;
            } else {

            }

        }
    }

    /**
     * @param array $config
     * get your data according to uid , domainId and type
     */
    public function getData (array $config)
    {
        $conn = db::getInstance();
        $queryBuilder = $conn->createQueryBuilder();
        $result = $queryBuilder
            ->select('*')
            ->from($this->tableName)
            ->where('uid = ? and domainId = ? and type = ?')
            ->orderBy('id' , 'ASC')
            ->setParameter(0 , $config['uid'])
            ->setParameter(1 , $config['domainId'])
            ->setParameter(2 , $config['type'])
            ->execute();
        $results = $result->fetchAll();
        $conn->close();
        return $results;
    }

    public static function findOne (array $config)
    {
        $conn = db::getInstance();
        $queryBuilder = $conn->createQueryBuilder();
        $data = $queryBuilder
            ->select('*')
            ->from('lmdns')
            ->where('id = :id')
            ->andWhere('uid = :uid')
            ->andWhere('domainId = :domainId')
            ->setParameter(':id' , $config['id'])
            ->setParameter(':uid' , $config['uid'])
            ->setParameter(':domainId' , $config['domainId'])
            ->execute();
        $data = $data->fetch();
        $model = new lmdns();
        $model->initData($data);
        $conn->close();
        return $model;
    }

    public function select ()
    {
        $conn = db::getInstance();
        $queryBuilder = $conn->createQueryBuilder();
        $result = $queryBuilder
            ->select('*')
            ->from($this->tableName)
            ->where('id = ?')
            ->setParameter(0 , $this->id)
            ->execute();
        $result = $result->fetch();
        $conn->close();
        return $result;
    }

    public function updateRecord ()
    {
        $conn = db::getInstance();
        $queryBuilder = $conn->createQueryBuilder();
        $this->validate();
        $queryBuilder
            ->update('lmdns')
            ->set('subdomain' , ':subdomain')
            ->set('value' , ':value')
            ->set('type' , ':type')
            ->set('ipIndex' , ':ipIndex')
            ->where('id = :id' )
            ->setParameter(':subdomain' , $this->subdomain)
            ->setParameter(':value' , $this->value)
            ->setParameter(':type' , $this->type)
            ->setParameter(':ipIndex' , $this->ipIndex)
            ->setParameter(':id', $this->id);
        $conn->close();
        return $queryBuilder->execute();
    }

    public function addRecord ()
    {
        $conn = db::getInstance();
        $queryBuilder = $conn->createQueryBuilder();
        $this->validate();
        $queryBuilder
            ->insert('lmdns')
            ->values(
                array(
                    'uid' => '?',
                    'domainId' => '?',
                    'type' => '?',
                    'subdomain' => '?',
                    'value' => '?',
                    'ipIndex' => '?',
                )
            )
            ->setParameter(0, $this->uid)
            ->setParameter(1, $this->domainId)
            ->setParameter(2, $this->type)
            ->setParameter(3, $this->subdomain)
            ->setParameter(4, $this->value)
            ->setParameter(5, $this->ipIndex)
        ;

        $result = $queryBuilder->execute();
        $conn->close();
        return $result;
    }

    public function delRecord ()
    {
        $conn = db::getInstance();
        $queryBuilder = $conn->createQueryBuilder();
        $queryBuilder->delete('lmdns')
            ->where('id = :record_id')
            ->andWhere('uid = :uid')
            ->andWhere('domainId = :domainId')
            ->setParameter(':record_id', $this->id)
            ->setParameter(':uid' , $this->uid)
            ->setParameter(':domainId' , $this->domainId)
            ->execute();

        $result = $queryBuilder->execute();
        $conn->close();
        return $result;

    }

    public function ReloadIpIndex ($uid , $domainId , $type)
    {
        $data = $this->getData(['uid' => $uid , 'domainId' => $domainId , 'type' => $type]);
        $i = 1;
        foreach ($data as $d)
        {
            $model = new lmdns();
            $model->initData($d);
            $model->ipIndex = $i;
            $model->updateRecord();
            $i++;
        }
    }

    public function getNewIndex ()
    {
        $conn = db::getInstance();
        $queryBuilder = $conn->createQueryBuilder();
        $result = $queryBuilder
             ->select('ipIndex')
             ->from($this->tableName)
             ->where('uid = ? and domainId = ? and type = ?')
             ->orderBy('ipIndex' , 'DESC')
             ->setParameter(0 , $this->uid)
             ->setParameter(1 , $this->domainId)
             ->setParameter(2 , $this->type)
             ->execute();
        $results = $result->fetch();
        $conn->close();
        if (!isset($results['ipIndex'])) {
            return 1;
        } else {
            return $results['ipIndex'] + 1;
        }
    }

    public function getNewRecordId ()
    {
        $conn = db::getInstance();
        $sql = "SELECT LAST_INSERT_ID()";

        $stmt = $conn->query($sql); // Simple, but has several drawbacks

        $result = $stmt->fetchColumn(0);

        $conn->close();

        return $result;//返回id
    }

    public function validate ()
    {
        if ($this->type == '') {
            $this->type = 'A';
        }
        if ($this->subdomain == '') {
            $this->subdomain = '@';
        }
    }
}