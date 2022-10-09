<?php

namespace App\Utils;

class View{

    private static $vars = [];

    public static function init($vars = []){
        self::$vars = $vars;
    }

    private static function getContentView($view){
        $file = __DIR__.'/../../resources/view/'.$view.'.html';
        return file_exists($file) ? file_get_contents($file) : '';
    }

    public static function render($view, $vars = []){
        $contentView = self::getContentView($view);

        // Merge de variáveis da View
        $vars = array_merge(self::$vars, $vars);
        
        // Chaves do Array de Variáveis
        $keys = array_keys($vars);
        $keys = array_map(function($item){
            return '{{'.$item.'}}';
        }, $keys);

        return str_replace($keys,array_values($vars),$contentView);
    }
}