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

class lmdns
{
    public $uid;

    public $domainId;

    public $type;

    public $subdomain;

    public $value;

    public $index;


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

            $this->{$key} = $value;

        }

    }

    public function getNewIndex ()
    {
        
    }

    public function getNewRecordId ()
    {
        $sql = "SELECT LAST_INSERT_ID()";

        $stmt = $this->conn->query($sql); // Simple, but has several drawbacks

        $result = $stmt->fetchColumn(0);

        return $result;//返回id
    }

    public function addRecord ()
    {

        $this->queryBuilder
            ->insert('lmdns')
            ->values(
                array(
                    'uid' => '?',
                    'domainId' => '?',
                    'type' => '?',
                    'subdomain' => '?',
                    'value' => '?',
                )
            )
            ->setParameter(0, $this->uid)
            ->setParameter(1, $this->domainId)
            ->setParameter(2, $this->type)
            ->setParameter(3, $this->subdomain)
            ->setParameter(4, $this->value)
        ;

        return $this->queryBuilder->execute();


    }
}