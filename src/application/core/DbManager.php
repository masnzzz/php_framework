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
}
