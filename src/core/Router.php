<?php

class Router
{
    protected $routes;

    /**
     * Router constructor.
     * @param $definitions
     */
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



    /**
     * PATHとルーティング定義配列のマッチングをして
     * ルーティングパラメータの特定を行う
     * 
     * @param string $path_info
     * @return array|false $params
     * 
     */
    public function resolve(string $path_info): array
    {
        if ('/' !== substr($path_info, 0, 1)) {
            // PATH_INFOの先頭がスラッシュではない場合、スラッシュを付与
            $path_info = '/' . $path_info;
        }
        
        foreach ($this->routes as $pattern => $params) {
            // $routesは変換済みのルーティング配列定義
            if (preg_match('#^' . $pattern . '$#', $path_info, $matches)) {
                // 正規表現でマッチング
                $params = array_merge($params, $matches);
                
                return $params;
            }
        }
        
        return false;
    }
}
