<?php
namespace app\kahuna\client\controller;

use app\kahuna\client\controller\AgentController;
use app\kahuna\client\controller\AgentAuthController;
use \Twig\Environment;


class AgentRouteController extends AgentController
{
    private static ?Environment $twig_agent = null;

    public static function showView(string $view, ?array $params = []): void 
    {
        echo self::$twig_agent->render("$view.twig", $params);
    }

    public static function setEnvironment(Environment $twig_agent)
    {
        self::$twig_agent = $twig_agent;
    }

    /**Views Agent ----------------- */
    public static function viewDefaultAgent(array $params, array $data):void
    {
        if(isset($_SESSION['api_user'])){
            $params['login'] = self::checkToken();
        }else{
            $params['login'] = false;
        }
        self::showView('default_agent', $params);
    }
    public static function viewLoginAgent(array $params, array $data):void
    {
        self::showView('login_agent', $params);
    }
   
    public static function viewCreateProductAgent(array $params, array $data):void
    {
        if(isset($_SESSION['api_user'])){
            $params['login']  = self::checkToken();
            if($params['login']){
                self::showView('create_product_agent', $params);
            }else{
                self::showView('default_agent', $params);                  
            }
        }else{
            $params['login'] = false;
            self::showView('default_agent', $params);
        }
    }
    public static function viewTicketsAgent(array $params, array $data):void
    {
        if(isset($_SESSION['api_user'])){
            $params['login'] = self::checkToken();
            if($params['login']){
                $tickets = json_decode(self::req('GET', '/tickets', $data), false);
                $params['tickets'] = $tickets->data;
                self::showView('tickets_list_agent', $params);
            }else{
                self::showView('default_agent', $params);
            }
        }else{
            self::showView('default_agent', $params);            
        }

    }
    
    public static function viewSingleTicketAgent(array $params, array $data):void
    {
        if(isset($_SESSION['api_user'])){
            $params['login'] = self::checkToken();
            if($params['login']){
                $ticket = json_decode(self::req('GET', "/ticket/{$params['ticket_id']}", $data), false);
                $params['ticket'] = $ticket->data[0];
                $params['info'] = self::getCustomerProductInfo($ticket->data[0]->customerproductId);
                self::showView('ticket_reply_agent', $params);
            }else{
                self::showView('default_agent', $params);
            }
        }else{
            self::showView('default_agent', $params);            
        }
    }
    
    /**----------------- */


    
    /** Actions Agent----------------- */
    
    public static function actionLoginAgent(array $params, array $data):void
    {
        $result = AgentAuthController::login($params, $data);
        if(isset($result->data)){
            $_SESSION['api_user'] = $result->data->agent;
            $_SESSION['api_token'] = $result->data->token;
            $tokenValid = self::checkToken();
            if($tokenValid){
                $params['login'] = true;
                self::showView('default_agent', $params);
            }
        }else{
            $params['error'] = "Login attempt failed";
            self::showView('login_agent', $params);
        }
    }

    public static function actionCreateProductAgent(array $params, array $data)
    {
        if(isset($_SESSION['api_user'])){
            $params['login'] = self::checkToken();
            if($params['login']){
                self::req('POST', '/product', $data);
                $params['product'] = true;
            }
            self::showView('default_agent', $params);
        }else{
            self::showView('default_agent', $params);
        }
    }

    
    public static function actionLogoutAgent(array $params, array $data)
    {
        AgentAuthController::logout();
        $params['login'] = false;
        self::showView('default_agent', $params);
    }

    public static function actionTicketAgent(array $params, array $data)
    {   
        var_dump($data);
        if(isset($_SESSION['api_user'])){
            $params['login'] = self::checkToken();
            if($params['login']){
                self::req('POST', '/ticket', $data);
                $params['ticket'] = true;
            }
            self::showView('default_agent', $params);
        }else{
            self::showView('default_agent', $params);
        }
    }





    /**----------------- */



}