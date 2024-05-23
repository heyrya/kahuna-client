<?php
namespace app\kahuna\client\controller;

use \Twig\Environment;
use \app\kahuna\client\model\Customer;

class RouteController
{
    private static ?Environment $twig = null;

    public static function showView(string $view, ?array $params = []): void {
        // self::$currentView = ucfirst($view);
        // $params['currentView'] = self::$currentView;
        echo self::$twig->render("$view.twig", $params);
    }
    public static function setEnvironment(Environment $twig): void
    {
        self::$twig = $twig;
    }
    /**Views Customer ----------------- */

    // Login View
    public static function viewLoginCustomer(array $params, array $data): void
    {
        self::showView('login', $params);
    }
    
    // Register View
    public static function viewRegisterCustomer(array $params, array $data): void
    {
        self::showView('registration', $params);
    }
    
    // Default View
    public static function viewDefaultCustomer(array $params, array $data): void
    {
        if(isset($_SESSION['email'])){
            $params['login'] = true;
            self::showView('default', $params);
        }else{
            self::showView('default', $params);
        }
    }

    public static function viewProductsCustomer(array $params, array $data):void
    {

    }
    public static function viewAccountCustomer(array $params, array $data):void
    {

    }



    /**-------------------- */

    /**Customer actions */

    public static function actionRegisterCustomer(array $params, array $data): void
    {
        $result = Customer::registrationValidation($data);
        if(is_array($result)){
            self::showView('registration-fail', ["errors"=>$result]);
        }else{
            //TODO
            self::showView('default', ["register"=>true]);
        }
    }


    public static function actionLoginCustomer(array $params, array $data): void
    {
        $customer = new Customer(email: $data['email'], password: $data['password']);
        $customer = Customer::authenticate($customer);
        if($customer){
            $params['login'] = true;
            $_SESSION['email'] = $customer->getEmail();
            $_SESSION['name'] = $customer->getName();
            $_SESSION['surname'] = $customer->getSurname();
            $_SESSION['mob_no'] = $customer->getMobNo();
            self::showView('default', $params);

        }
    }

    public static function actionLogoutCustomer(array $params, array $data): void
    {
        AuthController::logout();
        $params['login'] = false;
        self::showView('default', $params);
    }

}
