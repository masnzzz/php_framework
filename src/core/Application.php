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

    /**
     * Application constructor.
     * @param bool $debug
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
     * @param bool $debug
     */
    protected function setDebugMode(bool $debug)
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
     * アプリケーションの初期化処理を行う
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
     * アプリケーションの設定
     */
    protected function configure()
    {
    }



    /**
     * アプリケーションのルートディレクトリへのパスを返す
     * 
     * @return string ルートディレクトリへのファイルシステム上の絶対パス
     */
    abstract public function getRootDir();



    /**
     * ルーティングを取得
     * 
     * @return array
     */
    abstract protected function registerRoutes();



    /**
     * デバッグモードが有効か判定する
     * 
     * @return bool
     */
    public function isDebugMode(): bool
    {
        return $this->debug;
    }



    /**
     * Requestオブジェクトを取得
     *
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }



    /**
     * Responseオブジェクトを取得
     *
     * @return Response
     */
    public function getResponse(): Response
    {
        return $this->response;
    }



    /**
     * Sessionオブジェクトを取得
     *
     * @return Session
     */
    public function getSession(): Session
    {
        return $this->session;
    }



    /**
     * DbManagerオブジェクトを取得
     *
     * @return DbManager
     */
    public function getDbManager(): DbManager
    {
        return $this->db_manager;
    }



    /**
     * コントローラファイルが格納されているディレクトリへのパスを取得
     *
     * @return string
     */
    public function getControllerDir(): string
    {
        return $this->getRootDir() . '/controllers';
    }



    /**
     * ビューファイルが格納されているディレクトリへのパスを取得
     *
     * @return string
     */
    public function getViewDir(): string
    {
        return $this->getRootDir() . '/views';
    }



    /**
     * モデルファイルが格納されているディレクトリへのパスを取得
     *
     * @return string
     */
    public function getModelDir(): string
    {
        return $this->getRootDir() . '/models';
    }



    /**
     * ドキュメントルートへのパスを取得
     *
     * @return string
     */
    public function getWebDir(): string
    {
        return $this->getRootDir() . '/web';
    }



    /**
     * rアプリケーションを実行する
     * 
     * @throws HttpNotFoundException ルートが見つからない場合
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
     * @param string $controller_name
     * @param string $action
     * @param array $params
     * 
     * @throws HttpNotFoundException コントローラが特定できない場合
     */
    public function runAction(string $controller_name, string $action, $params = array())
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
     * 指定されたコントローラ名から対応するControllerオブジェクトを取得
     * 
     * @param string $controller_class
     * @return Controller
     */
    protected function findController(string $controller_class): Controller
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
     * 404エラー画面を返す設定
     * 
     * @param Exception $e
     */
    protected function render404Page(Exception $e)
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
