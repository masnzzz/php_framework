<?php

class Request
{
    protected $content;
    protected $status_code = 200;
    protected $status_text = 'OK';
    protected $http_headers = array();

    /**
     * 各プロパティに設定された値を元にレスポンス送信を行う
     */
    public function send()
    {
        // ステータスコードを指定
        header('HTTP/1.1' . $this->status_code . ' ' . $this->status_text);
        
        foreach ($this->http_headers as $name => $value) {
            // $http_headersにHTTPレスポンスヘッダの指定があればheader関数で送信する
            header($name . ':' . $value);
        }
        
        // レスポンスの内容を送信する
        echo $this->content;
    }

    

    /**
     * クライアントに返す情報をcontentプロパティに格納する
     * 
     * @param mixed $content HTMLなどクライアントに返す情報
     */
    public function setContext($content)
    {
        $this->content = $content;
    }



    /**
     * HTTPのステータスコードを格納する
     *
     * @param string $status_code
     * @param string $status_text
     */
    public function setStatusCode(string $status_code, $status_text = '')
    {
        $this->status_code = $status_code;
        $this->status_text = $status_text;
    }
    


    /**
     * HTTPヘッダを連想配列形式で格納する
     * 
     * @param $name
     * @param $value
     */
    public function setHttpHeader($name,$value)
    {
        $this->http_headers[$name] = $value;
    }
    
    
    
    /**
     * HTTPメソッドがPOSTかどうかチェックする
     * 
     * @return bool
     */
    public function isPost(): bool
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
    public function getGet(string $name, $default = null)
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
    public function getPost(string $name, $default = null)
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
    public function getHost(): string
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
    public function isSsl(): bool
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
    public function getRequestUri(): string
    {
        return $_SERVER['REQUEST_URI'];
    }



    /**
     * ベースURLを取得する
     * 
     * @return string ベースURL
     */
    public function getBaseUrl(): string
    {
        $request_uri = $this->getRequestUri();
        $script_name = $_SERVER['SCRIPT_NAME'];
        
        // フロントコントローラの指定の有無をチェック
        if (0 === strpos($request_uri, $script_name)) {
            // フロントコントローラがURLに含まれる場合
            return $script_name;
        } elseif (0 === strpos($request_uri, dirname($script_name))) {
            // フロントコントローラが省略されている場合
            return rtrim(dirname($script_name), '/');
        }
        
        return '';
    }



    /**
     * PATH_INFOを取得する
     * 
     * @return string PATH_INFO
     */
    public function getPathInfo(): string
    {
        $base_url = $this->getBaseUrl();
        $request_uri = $this->getRequestUri();

        // REQUEST_URIにGETパラメータを取得
        $pos = strpos($request_uri, '?');
        
        if (false !== $pos) {
            // REQUEST_URIにGETパラメータが含まれた場合、値を取得
            $request_uri = substr($request_uri, 0, $pos);
        }

        // GETパラメータを除いたREQUEST_URIからベースURLを除いた値をPATH_INFOとして返す
        return (string)substr($request_uri, strlen($base_url));
    }
}
