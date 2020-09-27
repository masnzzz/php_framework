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
     * 接続を実行する
     * 
     * @param string $name connectionsプロパティのキー
     * @param array $params PDOクラスのコンストラクタに渡す情報の配列
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
        $con = new PDO(
            $params['dsn'],
            $params['user'],
            $params['password'],
            $params['options']
        );
        
        // PDOインスタンスを作成
        // PDO内部でエラー発生時に例外を発生させるためPDO::ATTR_ERRMODE属性をPDO::ERRMODE_EXCEPTIONに設定する
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $this->connections[$name] = $con;
    }



    /**
     * connectメソッドで接続したコネクションを取得する
     * 
     * @param null $name
     * @return array
     */
    public function getConnection($name = null)
    {
        if (is_null($name)) {
            // 名前の指定が無ければ最初に作成したPDOクラスのインスタンスを返す
            return current($this->connections);
        }
        
        return $this->connections[$name];
    }



    /**
     * repository_connection_mapプロパティにテーブルごとのRepositoryクラスを追加する
     * 
     * @param string $repository_name
     * @param string $name
     */
    public function setRepositoryConnectionMap(string $repository_name, string $name)
    {
        $this->repository_connection_map[$repository_name] = $name;
    }



    /**
     * Repositoryクラスに対応する接続情報を取得する
     * 
     * @param $repository_name
     * @return array
     */
    public function getConnectionForRepository($repository_name)
    {
        if (isset($this->repository_connection_map[$repository_name])) {
            $name = $this->repository_connection_map[$repository_name];
            $con = $this->getConnection($name);
        } else {
            $con = $this->getConnection();
        }
        
        return $con;
    }
}
