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
     * @param array $definitions
     * @return array $routes 変換済みのURL
     */
    public function compileRoutes(array $definitions): array
    {
        $routes = array();
        
        // ルート定義配列のキーに含まれるパラメータを正規表現でキャプチャできる形式にする
        foreach ($definitions as $url => $params) {
            // URLをスラッシュごとに分割する
            $tokens = explode('/', ltrim($url, '/'));
            
            foreach ($tokens as &$token) {
                if (0 === strpos($token, ':')) {
                    // コロンの後に続く動的パラメータがあった場合
                    $name = substr($token, 1);
                    $token = '(?P<' . $name . '>[^/]+)';
                }
            }
            
            $pattern = '/' . implode('/', $tokens);
            $routes[$pattern] = $params;
        }
        
        return $routes;
    }
}
