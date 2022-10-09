<?php

namespace App\Controller\Pages;

use App\Model\Entity\Organization\Organization;
use App\Utils\View;

class Home extends Page{
    public static function getHome(){
        $obOrganization = new Organization;

        $content = View::render('pages/home', [
            'name' => $obOrganization->name,
        ]);

        return parent::getPage('HOME > WDEV', $content);
    }
}