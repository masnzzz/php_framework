<?php

/**
 * Class DbManager
 * 
 * DBとの接続情報（コネクション）を管理するクラス
 */
class DbManager
{
    /**
     * @var array 接続情報であるPDOクラスのインスタンス
     */
    protected $connections = array();

    /**
     * @var array Repositoryクラスと接続名の対応
     */
    protected $repository_connection_map = array();

    /**
     * @var array
     */
    protected $repositories = array();



    /**
     * データベースへ接続
     *
     * @param string $name
     * @param array $params
     */
    public function connect(string $name, array $params)
    {
        $params = array_merge(array(
            'dsn' => null,
            'user' => '',
            'password' => '',
            'options' => array(),
        ), $params);
        
        // PDOクラスのインスタンスを作成
        $connection = new PDO(
            $params['dsn'],
            $params['user'],
            $params['password'],
            $params['options']
        );
        
        // PDOインスタンスを作成
        // PDO内部でエラー発生時に例外を発生させるためPDO::ATTR_ERRMODE属性をPDO::ERRMODE_EXCEPTIONに設定する
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $this->connections[$name] = $connection;
    }



    /**
     * connectメソッドで接続したコネクションを取得する
     * 
     * @param null $name
     * @return PDO
     */
    public function getConnection($name = null): PDO
    {
        if (is_null($name)) {
            // 名前の指定が無ければ最初に作成したPDOクラスのインスタンスを返す
            return current($this->connections);
        }
        
        return $this->connections[$name];
    }



    /**
     * リポジトリごとのコネクション情報を設定
     * 
     * @param string $repository_name
     * @param string $name
     */
    public function setRepositoryConnectionMap(string $repository_name, string $name)
    {
        $this->repository_connection_map[$repository_name] = $name;
    }



    /**
     * 指定されたリポジトリに対応するコネクションを取得
     * 
     * @param $repository_name
     * @return PDO
     */
    public function getConnectionForRepository($repository_name): PDO
    {
        if (isset($this->repository_connection_map[$repository_name])) {
            $name = $this->repository_connection_map[$repository_name];
            $connection = $this->getConnection($name);
        } else {
            $connection = $this->getConnection();
        }
        
        return $connection;
    }



    /**
     * リポジトリクラスのインスタンスを生成して取得する
     *
     * @param string $repository_name
     * @return DbRepository
     */
    public function get(string $repository_name): DbRepository
    {
        if (!isset($this->repositories[$repository_name])) {
            // Repositoryのクラス名を指定
            $repository_class = $repository_name . 'Repository';
            // コネクションを取得
            $connection = $this->getConnectionForRepository($repository_name);
            // 動的にインスタンス生成
            $repository = new $repository_class($connection);
            // インスタンスを保持し$repositoriesに格納
            $this->repositories[$repository_name] = $repository;
        }

        return $this->repositories[$repository_name];
    }



    public function __destruct()
    {
        foreach ($this->repositories as $repository) {
            unset($repository);
        }
        
        foreach ($this->connections as $connection) {
            unset($connection);
        }
    }
}
