<?php
namespace app\kahuna\client\controller;

use app\kahuna\client\model\Product;
use \Twig\Environment;
use \app\kahuna\client\model\Customer;
use \stdClass;

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
        if(isset($_SESSION['email'])){
            $customerId = filter_var($_SESSION['id'], FILTER_VALIDATE_INT);
            $products = Product::getProductsCustomer($customerId);
            $params['products'] = $products;
            self::showView('products-list', $params);
        }else{
            self::showView('default', $params);
        }
    }
    public static function viewAccountCustomer(array $params, array $data):void
    {

    }

    public static function viewRegisterProductCustomer(array $params, array $data):void
    {
        if(isset($_SESSION['email'])){
            $products = Product::getProductsUnregistered();
            $products_arr = [];
            foreach($products as $key=> $product){
                $products_arr[$key] = new stdClass; 
                $products_arr[$key]->id = $product->getId();
                $products_arr[$key]->serialId = $product->getSerialId();
                $products_arr[$key]->name = $product->getName();
                $products_arr[$key]->warranty = $product->getWarranty();
            }
            print_r($products_arr); 
            $params['products'] = $products_arr;
            self::showView('product-register', $params);
        }else{
            self::showView('default', $params);
        }
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
            $_SESSION['id'] = $customer->getId();
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

    public static function actionRegisterProductCustomer(array $params, array $data): void
    {
        $productId = filter_input(INPUT_POST, 'product_register_id', FILTER_VALIDATE_INT);
        $customerId = filter_var($_SESSION['id'], FILTER_VALIDATE_INT);
        $product = new Product(id: $productId);
        $product = Product::productRegisterCustomer($product, $customerId); 
        var_dump($product);
        $params['product'] = new stdClass;
        $params['product']->serialId = $product->getSerialId();  
        $params['product']->name = $product->getName();  
        $params['product']->warranty = $product->getWarranty();  
        $params['product_register'] = true;
        self::showView('default', $params);
    }

}
