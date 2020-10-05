<?php

class Session
{
    protected static $sessionStarted = false;
    protected static $sessionIdRegenerated = false;

    /**
     * Session constructor.
     * 自動的にセッションを開始する
     */
    public function __construct()
    {
        if (!self::$sessionStarted) {
            session_start();

            self::$sessionStarted = true;
        }
    }


    
    /**
     * $_SESSIONへの設定を行う
     * 
     * @param $name
     * @param $value
     */
    public function set($name, $value)
    {
        $_SESSION[$name] = $value;
    }


    
    /**
     * $_SESSIONの取得を行う
     * 
     * @param $name
     * @param null $default
     * @return mixed|null
     */
    public function get($name, $default = null)
    {
        if (isset($_SESSION[$name])) {
            return $_SESSION[$name];
        }
        return $default;
    }


    
    /**
     * $_SESSIONから指定した値を削除する
     * 
     * @param $name
     */
    public function remove($name)
    {
        unset($_SESSION[$name]);
    }


    
    /**
     * $_SESSIONをからにする
     */
    public function clear()
    {
        $_SESSION = array();
    }


    
    /**
     * セッションIDの発行を行う
     * 
     * @param bool $destroy
     */
    public function regenerate($destroy = true)
    {
        if (!self::$sessionIdRegenerated) {
            // 複数回リクエストが呼び出されないように静的プロパティでチェック
            session_regenerate_id($destroy);

            self::$sessionIdRegenerated = true;
        }
    }


    
    /**
     * ユーザーのログイン状態の変更を行う
     * 
     * @param $bool
     */
    public function setAuthenticated($bool)
    {
        $this->set('_authenticated', (bool)$bool);

        $this->regenerate();
    }


    
    /**
     * ログインしているかどうかの判定を行う
     * 
     * @return mixed|null
     */
    public function isAuthenticated()
    {
        return $this->get('_authenticated', false);
    }
}
