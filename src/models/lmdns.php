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

    /**
     * @var \Doctrine\DBAL\Connection
     */
    public $conn;

    /**
     * @var \Doctrine\DBAL\Query\QueryBuilder
     */
    public $queryBuilder;

    public function __construct()
    {

        $this->conn = \liumapp\dns\models\db::getInstance();

        $this->queryBuilder = $this->conn->createQueryBuilder();

    }

    public function initData(array $data)
    {
        foreach ($data as $key => $value) {
            if (isset($this->{$key})) {
                $this->{$key} = $value;
            }
        }
    }

    /**
     * @param array $config
     * get your data according to uid , domainId and type
     */
    public function getData (array $config)
    {
        $result = $this->queryBuilder
            ->select('*')
            ->from($this->tableName)
            ->where('uid = ? and domainId = ? and type = ?')
            ->orderBy('ipIndex' , 'ASC')
            ->setParameter(0 , $config['uid'])
            ->setParameter(1 , $config['domainId'])
            ->setParameter(2 , $config['type'])
            ->execute();
        $results = $result->fetchAll();
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
        return $model;
    }

    public function select ()
    {
        $result = $this->queryBuilder
            ->select('*')
            ->from($this->tableName)
            ->where('id = ?')
            ->setParameter(0 , $this->id)
            ->execute();
        return $result->fetch();
    }

    public function updateRecord ()
    {
        $this->validate();
        $status = $this->queryBuilder
            ->update('lmdns')
            ->set('subdomain' , $this->subdomain)
            ->set('value' , $this->value)
            ->set('type' , $this->type)
            ->set('ipIndex' , $this->ipIndex)
            ->where('id = ?' )
            ->setParameter(0, $this->id)
            ->execute();
        return $status;
    }

    public function addRecord ()
    {
        $this->validate();
        $this->queryBuilder
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

        return $this->queryBuilder->execute();

    }

    public function delRecord ()
    {

        $this->queryBuilder->delete('lmdns')
            ->where('id = :record_id')
            ->andWhere('uid = :uid')
            ->andWhere('domainId = :domainId')
            ->setParameter(':record_id', $this->id)
            ->setParameter(':uid' , $this->uid)
            ->setParameter(':domainId' , $this->domainId)
            ->execute();

        return $this->queryBuilder->execute();

    }

    public function ReloadIpIndex ($uid , $domainId , $type)
    {
        $data = $this->getData(['uid' => $uid , 'domainId' => $domainId , 'type' => $type]);
        $i = 1;foreach ($data as $d)
        {
            $ipIndex = $d['ipIndex'];
            $model = new lmdns();
            $model->initData([$d]);
            $model->ipIndex = $i;
            $model->updateRecord();
            $i++;
        }
    }

    public function getNewIndex ()
    {
        $result = $this->queryBuilder
             ->select('ipIndex')
             ->from($this->tableName)
             ->where('uid = ? and domainId = ? and type = ?')
             ->orderBy('ipIndex' , 'DESC')
             ->setParameter(0 , $this->uid)
             ->setParameter(1 , $this->domainId)
             ->setParameter(2 , $this->type)
             ->execute();
        $results = $result->fetch();
        if (!isset($results['ipIndex'])) {
            return 1;
        } else {
            return $result['ipIndex'] + 1;
        }
    }

    public function getNewRecordId ()
    {
        $sql = "SELECT LAST_INSERT_ID()";

        $stmt = $this->conn->query($sql); // Simple, but has several drawbacks

        $result = $stmt->fetchColumn(0);

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