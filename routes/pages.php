<?php

use App\Controller\Pages\About;
use App\Controller\Pages\Home;
use App\Controller\Pages\Testimony;
use App\Http\Response;

$obRouter->get('/', [
    function(){
        return new Response(200, Home::getHome());
    }
]);

$obRouter->get('/sobre', [
    function(){
        return new Response(200, About::getAbout());
    }
]);

$obRouter->get('/depoimentos', [
    function($request){
        return new Response(200, Testimony::getTestimonies($request));
    }
]);

$obRouter->post('/depoimentos', [
    function($request){
        return new Response(200, Testimony::insertTestimony($request));
    }
]);
