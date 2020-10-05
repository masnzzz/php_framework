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
}
