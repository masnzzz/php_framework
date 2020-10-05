<?php

class Response
{
    protected $content;
    protected $status_code = 200;
    protected $status_text = 'OK';
    protected $http_headers = array();

    /**
     * レスポンスを送信
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
     * コンテンツを設定
     * 
     * @param string $content HTMLなどクライアントに返す情報
     */
    public function setContent($content)
    {
        $this->content = $content;
    }



    /**
     * HTTPのステータスコードを格納する
     *
     * @param int $status_code
     * @param string $status_text
     */
    public function setStatusCode(int $status_code, $status_text = '')
    {
        $this->status_code = $status_code;
        $this->status_text = $status_text;
    }



    /**
     * HTTPレスポンスヘッダを設定
     * 
     * @param string $name
     * @param mixed $value
     */
    public function setHttpHeader(string $name, $value)
    {
        $this->http_headers[$name] = $value;
    }
}
