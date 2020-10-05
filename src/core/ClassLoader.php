<?php

class ClassLoader
{
    /**
     * @var array $dirs 読み込むクラスファイルを格納 
     */
    protected $dirs;

    /**
     * 自身をオートロードスタックに登録
     */
    public function register()
    {
        spl_autoload_register(array($this, 'loadClass'));
    }



    /**
     * オートロード対象のディレクトリを登録
     * 
     * @param string $dir
     */
    public function registerDir($dir)
    {
        $this->dirs[] = $dir;
    }



    /**
     * オートロード時に自動的に呼び出されクラスファイルの読み込みを行う
     * 
     * @param string $class
     */
    public function loadClass($class)
    {
        foreach ($this->dirs as $dir) {
            $file = $dir . '/' . $class . '.php';
            if (is_readable($file)) {
                require $file;
                
                return;
            }
        }
    }
}
