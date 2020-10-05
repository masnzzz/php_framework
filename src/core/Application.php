<?php

/**
 * Class Application
 * 
 * 基本機能
 * オブジェクトの管理
 * ルーティング定義
 * コントローラ実行
 * レスポンス送信
 */
abstract class Application
{
    protected $debug = false;
    protected $request;
    protected $response;
    protected $session;
    protected $db_manager;
    protected $router;
    protected $login_action = array();

    /**
     * Application constructor.
     * @param false $debug
     */
    public function __construct($debug = false)
    {
        $this->setDebugMode($debug);
        $this->initialize();
        $this->configure();
    }



    /**
     * デバッグモードに応じてエラー表示処理を変更する
     * 
     * @param $debug
     */
    protected function setDebugMode($debug)
    {
        if ($debug) {
            $this->debug = true;
            ini_set('display_errors', 1);
            error_reporting(-1);
        } else {
            $this->debug = false;
            ini_set('display_errors', 0);
        }
    }



    /**
     * クラスの初期化処理を行う
     */
    public function initialize()
    {
        $this->request = new Request();
        $this->response = new Response();
        $this->session = new Session();
        $this->db_manager = new DbManager();
        // Routerクラスのインスタンス生成時にルーティング定義配列を返す
        $this->router = new Router($this->registerRoutes());
    }



    /**
     * 個別のアプリケーションで設定をする
     */
    public function configure()
    {
    }



    /**
     * アプリケーションのルートディレクトリへのパスを返す
     */
    abstract public function getRootDir();



    /**
     * ルーティング定義配列を返す
     */
    abstract protected function registerRoutes();



    /**
     * デバッグモードが有効か判定する
     * 
     * @return bool
     */
    public function isDebugMode()
    {
        return $this->debug;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function getSession()
    {
        return $this->session;
    }

    public function getDbManager()
    {
        return $this->db_manager;
    }

    public function getControllerDir()
    {
        return $this->getRootDir() . '/controllers';
    }

    public function getViewDir()
    {
        return $this->getRootDir() . '/views';
    }

    public function getModelDir()
    {
        return $this->getRootDir() . '/models';
    }

    public function getWebDir()
    {
        return $this->getRootDir() . '/web';
    }



    /**
     * runActionに値を渡し、レスポンス送信する
     * 
     * @throws HttpNotFoundException
     */
    public function run()
    {
        try {
            $params = $this->router->resolve($this->request->getPathInfo());
            if ($params === false) {
                // PATHパラメータの読み込みに失敗した場合
                throw new HttpNotFoundException('No route found for ' . $this->request->getPathInfo());
            }

            $controller = $params['controller'];
            $action = $params['action'];

            $this->runAction($controller, $action, $params);
            
        } catch (HttpNotFoundException $e) {
            $this->render404Page($e);
            
        } catch (UnauthorizedActionException $e) {
            list($controller, $action) = $this->login_action;
            $this->runAction($controller, $action);
        }

        $this->response->send();
    }



    /**
     * アクションを実行
     *
     * @param $controller_name
     * @param $action
     * @param array $params
     * @throws HttpNotFoundException
     */
    public function runAction($controller_name, $action, $params = array())
    {
        $controller_class = ucfirst($controller_name) . 'Controller';
        
        $controller = $this->findController($controller_class);
        if ($controller === false) {
            // コントローラクラスの読み込みに失敗した場合
            throw new HttpNotFoundException($controller_class . ' controller is not found');
        }
        
        // アクションを実行する
        $content = $controller->run($action, $params);
        
        $this->response->setContent($content);
    }



    /**
     * コントローラークラスファイルを読み込む
     * 
     * @param string $controller_class
     * @return false|mixed
     */
    protected function findController(string $controller_class)
    {
        if (!class_exists($controller_class)) {
            // クラスが定義済みではなかった場合
            $controller_file = $this->getControllerDir() . '/' . $controller_class . '.php';
            
            if (!is_readable($controller_file)) {
                // 読み込みに失敗した場合
                return false;
            } else {
                // 読み込みに成功した場合コントローラファイルを読み込む
                require_once $controller_file;
                
                if (!class_exists($controller_class)) {
                    return false;
                }
            }
        }
        
        // コントローラークラスを生成する
        return new $controller_class($this);
    }



    /**
     * 404エラーを返す
     * 
     * @param $e
     */
    protected function render404Page($e)
    {
        $this->response->setStatusCode(404, 'Not Found');
        $message = $this->isDebugMode() ? $e->getMessage() : 'Page not found.';
        $message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
        
        $this->response->setContent(<<<EOF
<!DOCTYPE>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <title>404</title>
    </head>
    <body>
        {$message}
    </body>
</html>
EOF
        );
    }
}
