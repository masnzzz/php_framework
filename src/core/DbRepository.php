<?php

abstract class DbRepository
{
    protected $connection;

    /**
     * Undocumented function
     *
     * @param PDO $connection
     */
    public function __construct(PDO $connection)
    {
        $this->setConnection($connection);
    }



    /**
     * コネクションを設定
     *
     * @param PDO $connection
     */
    public function setConnection(PDO $connection)
    {
        $this->connection = $connection;
    }



    /**
     * クエリを実行
     *
     * @param string $sql
     * @param array $params
     * @return PDOStatement $stmt
     */
    public function execute(string $sql, $params = array()): PDOStatement
    {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);

        return $stmt;
    }



    /**
     * クエリを実行し、結果を1行取得
     *
     * @param string $sql
     * @param array $params
     * @return array
     */
    public function fetch(string $sql, array $params = array()): array
    {
        return $this->execute($sql, $params)->fetch(PDO::FETCH_ASSOC);
    }



    /**
     * クエリを実行し、結果をすべて取得
     *
     * @param string $sql
     * @param array $params
     * @return array
     */
    public function fetchAll($sql, $params = array())
    {
        return $this->execute($sql, $params)->fetchAll(PDO::FETCH_ASSOC);
    }
}
