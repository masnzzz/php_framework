<?php

class Response
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
    public function setContent($content)
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
}
