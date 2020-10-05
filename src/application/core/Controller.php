<?php

abstract class Controller
{
    protected $controller_name;
    protected $action_name;
    protected $application;
    protected $request;
    protected $response;
    protected $session;
    protected $db_manager;
    
    public function __construct($application)
    {
        // コントローラ名からクラス名を逆算してプロパティに設定
        $this->controller_name = strtolower(substr(get_class($this), 0, -10));
        
        $this->application = $application;
        $this->request     = $application->getRequest();
        $this->response    = $application->getResponse();
        $this->session     = $application->getSession();
        $this->db_manager  = $application->getDbManager();
    }



    /**
     * アクションを実行
     * 
     * @param $action
     * @param array $params
     * @return mixed
     */
    public function run($action, $params = array())
    {
        $this->action_name = $action;
        
        // アクション名となるメソッド名をプロパティに格納
        $action_method = $action . 'Action';
        if (!method_exists($this, $action_method)) {
            // $action_methodの値のメソッドが存在しない場合
            $this->forward404();
        }
        
        // アクションを実行
        return $this->$action_method($params);
    }


    /**
     * レンダリングを実行する
     * ビューファイルの読み込み処理をラッピングして返す
     *
     * @param array $variables
     * @param null $template HTMLテンプレート
     * @param string $layout
     * @return false|mixed|string
     */
    protected function render($variables = array(), $template = null, $layout = 'layout')
    {
        // デフォルト値を連想配列で指定する
        $defaults = array(
            'request' => $this->request,
            'base_url' => $this->request->getBaseUrl(),
            'session' => $this->session,
        );
        
        // Viewクラスのインスタンスを作成 デフォルト値を引数として指定
        $view = new View($this->application->getViewDir(), $defaults);
        
        if (is_null($template)) {
            // テンプレート名が指定されてない場合はアクション名をファイル名として指定する
            $template = $this->action_name;
        }
        
        // コントローラ名をテンプレート名の先頭に付与する
        $path = $this->controller_name . '/' . $template;
        
        // ビューファイルの読み込みを実行する
        return $view->render($path, $variables, $layout);
    }
}
