<?php

class ClassLoader
{
    protected $dirs;

    /**
     * PHPにオートローダークラスを登録する
     */
    public function register()
    {
        spl_autoload_register(array($this, 'loadClass'));
    }



    /**
     * 渡されたクラスファイルの読み込みを行い登録する
     */
    public function registerDir($dir)
    {
        $this->dirs[] = $dir;
    }



    /**
     * オートロード時に自動的に呼び出されクラスファイルの読み込みを行う
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