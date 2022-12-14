<?php

namespace App\Controller\Pages;

use App\Model\Entity\Organization\Organization;
use App\Utils\View;

class About extends Page{
    public static function getAbout(){
        $obOrganization = new Organization;

        $content = View::render('pages/about', [
            'name' => $obOrganization->name,
            'description' => $obOrganization->description,
            'site' => $obOrganization->site,
        ]);

        return parent::getPage('SOBRE > WDEV', $content);
    }
}