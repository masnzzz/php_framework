<?php

class Request
{
    /**
     * HTTPメソッドがPOSTかどうかチェックする
     * 
     * @return boolean
     */
    public function isPost()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // POSTであればtrueを返す
            return true;
        }

        return false;
    }



    /**
     * $_GET変数から値を取得する
     * 
     * @param string $name
     * @param null $default
     * @return string|null
     */
    public function getGet($name, $default = null)
    {
        if (isset($_GET[$name])) {
            return $_GET[$name];
        }

        return $default;
    }



    /**
     * $_POST変数から値を取得する
     * 
     * @param string $name
     * @param null $default
     * @return string|null
     */
    public function getPost($name, $default = null)
    {
        if (isset($_GET[$name])) {
            return $_GET['name'];
        }

        return $default;
    }



    /**
     * サーバのホスト名を取得する
     * リダイレクトを行う場合などに使用
     * 
     * @return string サーバのホスト名
     */
    public function getHost()
    {
        if (!empty($_SERVER['HTTP_HOST'])) {
            return $_SERVER['HTTP_HOST'];
        }

        // ホスト名が取得できない場合はApache側で設定されたホスト名の値を返す
        return $_SERVER['SERVER_NAME'];
    }



    /**
     * HTTPSでアクセスされたかどうかチェックする
     * 
     * @return boolean
     */
    public function isSsl()
    {
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            // HTTPSでアクセスされた場合、$_SERVER['HTTPS']に'on'が含まれる
            return true;
        }

        return false;
    }



    /**
     * リクエストされたURLの情報を取得する
     * 
     * @return string $_SERVER['REQUEST_URI'] URLのホスト部分以降の値
     */
    public function getRequestUri()
    {
        return $_SERVER['REQUEST_URI'];
    }
}