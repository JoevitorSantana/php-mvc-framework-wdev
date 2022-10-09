<?php

namespace App\Http;

use Closure;
use Exception;
use ReflectionFunction;

class Router{
    private $url = '';
    private $prefix = '';
    private $router = [];
    private $request;

    public function __construct($url)
    {
        $this->request = new Request($this);
        $this->url     = $url;
        $this->setPrefix();
    }

    private function setPrefix(){
        $parseUrl = parse_url($this->url);

        $this->prefix = $parseUrl['path'] ?? '';
    }

    private function addRoute($method, $route, $params = []){
        
        // Validação dos parâmetros

        foreach($params as $key=>$value){
            if($value instanceof Closure){
                $params['controller'] = $value;
                unset($params[$key]);
                continue;
            }
        }

        // Variáveis da rota
        $params['variables'] = [];

        // padrão de validação da URL
        $patternVariable = '/{(.*)}/';
        if(preg_match_all($patternVariable, $route, $matches)){
            $route = preg_replace($patternVariable, '(.*?)', $route);
            $params['variables'] = $matches[1];
        }

        // Padrão de validação da URL
        $patternRoute = '/^'.str_replace('/', '\/',$route).'$/';
        
        // Adiciona a Rota por dentro da classe
        $this->routes[$patternRoute][$method] = $params;
    }

    /**
     * Método responsável por definir uma rota GET
     *
     * @param [type] $route
     * @param array $params
     * @return void
     */
    public function get($route, $params = []){
        return $this->addRoute('GET', $route, $params);
    }

    /**
     * Método responsável por definir uma rota POST
     *
     * @param [type] $route
     * @param array $params
     * @return void
     */
    public function post($route, $params = []){
        return $this->addRoute('POST', $route, $params);
    }

    /**
     * Método responsável por definir uma rota PUT
     *
     * @param [type] $route
     * @param array $params
     * @return void
     */
    public function put($route, $params = []){
        return $this->addRoute('PUT', $route, $params);
    }

    /**
     * Método responsável por definir uma rota DELETE
     *
     * @param [type] $route
     * @param array $params
     * @return void
     */
    public function delete($route, $params = []){
        return $this->addRoute('DELETE', $route, $params);
    }

    /**
     * Método responsável por retornar a URI desconsiderando o prefixo
     *
     * @return string
     */
    private function getUri(){
        // URI da Request
        $uri = $this->request->getUri();

        // Fatia a URI com o prefixo
        $xUri = strlen($this->prefix) ? explode($this->prefix, $uri) : [$uri];

        // Retorna a URI sem prefixo
        return end($xUri);
    }

    /**
     * Método responsável por retornar os dados da rota atual
     *
     * @return array
     */
    private function getRoute(){

        // URI
        $uri = $this->getUri();

        // Method
        $httpMethod = $this->request->getHttpMethod();

        // Valida as rotas
        foreach($this->routes as $patternRoute=>$methods){
            //Verifica se a URI bate o padrão
            if(preg_match($patternRoute, $uri, $matches)) {

                // Verfifica o método
                if(isset($methods[$httpMethod])){

                    //Remove a primeira posição
                    unset($matches[0]);

                    // Variáveis Processadas
                    $keys = $methods[$httpMethod]['variables'];
                    $methods[$httpMethod]['variables'] = array_combine($keys, $matches);
                    $methods[$httpMethod]['variables']['request'] = $this->request;

                    // Retorno dos parâmetros da rota
                    return $methods[$httpMethod];
                }

                // Método não permitido/definido
                throw new Exception("Método não permitido", 405);
            }
        }

        // URL não encontrada
        throw new Exception("URL não encontrada", 404);
    }


    public function run(){
        try{

            // Obtêm a rota atual
            $route = $this->getRoute();
            
            // Verifica o controlador
            if(!isset($route['controller'])){
                throw new Exception("A Url não pode ser processada", 500);
            };

            // Argumentos da função
            $args = [];
            // Reflection
            $reflection = new ReflectionFunction($route['controller']);
            foreach($reflection->getParameters() as $parameter){
                $name = $parameter->getName();
                $args[$name] = $route['variables'][$name] ?? '';
            }

            //Retorna a execução da função
            return call_user_func_array($route['controller'], $args);

        } catch(Exception $e) {
            return new Response($e->getCode(),$e->getMessage());
        }
    }

    public function getCurrentUrl(){
        return $this->url.$this->getUri();
    }
}