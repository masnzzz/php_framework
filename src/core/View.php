<?php

class View
{
    protected $base_dir;
    protected $defaults;
    protected $layout_variables = array();

    /**
     * View constructor.
     * @param string $base_dir viewsディレクトリへの絶対パス
     * @param array  $defaults viewファイルにデフォルトで渡す変数
     */
    public function __construct(string $base_dir, $defaults = array())
    {
        $this->base_dir = $base_dir;
        $this->defaults  = $defaults;
    }



    /**
     * $layout_variablesプロパティに値を設定する
     * 
     * @param string $name
     * @param mixed $value
     */
    public function setLayoutVar(string $name, $value)
    {
        // レイアウトファイルの読み込みを行う際に値を渡す
        $this->layout_variables[$name] = $value;
    }



    /**
     * ビューファイルをレンダリング
     * 
     * @param string $_path      ビューファイルへのパス
     * @param array  $_variables ビューファイルに渡す変数
     * @param mixed  $_layout    レイアウトファイル名 Controllerから呼び出された場合のみtrue
     * @return string
     */
    public function render(string $_path, $_variables = array(), $_layout = false): string
    {
        // base_dirの中からビューファイルのパスを取得する
        $_file = $this->base_dir . '/' . $_path . '.php';
        
        extract(array_merge($this->defaults, $_variables));
        
        // アウトプットバッファリングを用いてビューファイルを文字列として取得
        // アウトプットバッファリングを開始
        ob_start();
        // バッファの自動フラッシュを制御
        ob_implicit_flush(0);
        
        require $_file;
        
        // バッファの内容を$contentに格納
        $content = ob_get_clean();
        
        if ($_layout) {
            // Controllerクラスから呼び出され、レイアウトファイル名が指定された場合
            $content = $this->render(
                $_layout,
                array_merge($this->layout_variables, array(
                    '_content' => $content,
                )
            ));
        }
        
        return $content;
    }



    /**
     * HTML特殊文字をエスケープする
     * 
     * @param string $string
     * @return string htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
     */
    public function escape(string $string): string
    {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
}
