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
     * 
     * @return mixed
     */
    abstract public function getRootDir();



    /**
     * ルーティング定義配列を返す
     * 
     * @return mixed
     */
    abstract public function registerRoutes();

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
}
