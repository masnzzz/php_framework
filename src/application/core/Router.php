<?php

class Router
{
    protected $routes;
    
    public function __construct($definitions)
    {
        $this->routes = $this->compileRoutes($definitions);
    }



    /**
     * ルーティング定義配列を変換する
     * 
     * @param $definitions
     * @return array $routes 変換済みのURL
     */
    public function compileRoutes($definitions)
    {
        $routes = array();
        
        // ルート定義配列のキーに含まれるパラメータを正規表現でキャプチャできる形式にする
        foreach ($definitions as $url => $params) {
            // URLをスラッシュごとに分割する
            $tokens = explode('/', ltrim($url, '/'));
            
            foreach ($tokens as $i => $token) {
                if (0 === strpos($token, ':')) {
                    // コロンの後に続く動的パラメータがあった場合
                    $name = substr($token, 1);
                    $token = '(?P<' . $name . '>[^/]+)';
                }
                $tokens[$i] = $token;
            }
            
            $pattern = '/' . implode('/', $tokens);
            $routes[$pattern] = $params;
        }
        
        return $routes;
    }
}