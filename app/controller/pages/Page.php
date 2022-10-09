<?php

namespace App\Controller\Pages;

use App\Utils\View;

class Page{

    private static function getHeader(){
        return View::render('pages/header');
    }

    private static function getFooter(){
        return View::render('pages/footer');
    }

    /**
     * Método responsável pos renderizar o layout de paginação 
     *
     * @param [type] $request
     * @param [type] $obPagination
     * @return void
     */
    public static function getPagination($request, $obPagination){
        $pages = $obPagination->getPages();

        echo '<pre>';
        print_r($pages);
        echo '</pre>'; exit;


        // Verifica a quantidade de páginas
        if(count($pages) <= 1) return '';

        // Links
        $links = '';
        
        // Url atual (sem Gets)
        $url = $request->getRouter()->getCurrentUrl();

        //GET
        $queryParams = $request->getQueryParams();

        //Renderiza os Links
        foreach($pages as $page){
            // Altera a página
            $queryParams['page'] = $page['page'];

            //LINK
            $link = $url.'?'.http_build_query($queryParams);

            // VIEW
            $links .= View::render('pages/pagination/link', [
                'page' => $page['page'],
                'link' => $link,
                'active' => $page['current']  ? 'active' : ''
            ]);
        }

        // Renderiza BOX de Paginação
        return View::render('pages/pagination/box', [
            'links' => $links,
        ]);

    }

    /**
     * Método responsável por renderizar as páginas
     *
     * @param [type] $title
     * @param [type] $content
     * @return void
     */
    public static function getPage($title, $content){
        return View::render('pages/page', [
            'title' => $title,
            'header' => self::getHeader(),
            'content' => $content,
            'footer' => self::getFooter()
        ]);
    }
}