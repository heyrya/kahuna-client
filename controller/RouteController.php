<?php
namespace app\kahuna\client\controller;

use app\kahuna\client\model\Product;
use \Twig\Environment;
use \app\kahuna\client\model\Customer;
use \stdClass;

class RouteController
{
    private static ?Environment $twig_customer = null;

    public static function showView(string $view, ?array $params = []): void {
        // self::$currentView = ucfirst($view);
        // $params['currentView'] = self::$currentView;
        echo self::$twig_customer->render("$view.twig", $params);
    }
    public static function setEnvironment(Environment $twig_customer): void
    {
        self::$twig_customer = $twig_customer;
    }
    /**Views Customer ----------------- */

    // Default View
    public static function viewDefaultCustomer(array $params, array $data): void
    {
        if(isset($_SESSION['customerId'])){
            $params['login'] = true;
            self::showView('default', $params);
        }else{
            self::showView('default', $params);
        }
    }
    
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
    

    public static function viewProductsCustomer(array $params, array $data):void
    {
        if(isset($_SESSION['customerId'])){
            $customerId = filter_var($_SESSION['customerId'], FILTER_VALIDATE_INT);
            $products = Product::getProductsCustomer($customerId);
            $params['products'] = $products;
            $params['login'] = true;
            self::showView('products-list', $params);
        }else{
            self::showView('default', $params);
        }
    }
    public static function viewAccountCustomer(array $params, array $data):void
    {

    }

    public static function viewProductCustomer(array $params, array $data):void
    {   
        echo "viewProductCustomer is invoked.";

        if(isset($_SESSION['customerId'])){
            
            $params['login'] = true;
            $product = Product::getProduct($params['product_id']);
            $params['product'] = $product;
            self::showView('products-list', $params);
        }else{
            self::showView('default', $params);
        }
    }

    public static function viewRegisterProductCustomer(array $params, array $data):void
    {
        if(isset($_SESSION['customerId'])){
            $products = Product::getProductsUnregistered();
            // $products_arr = [];
            // foreach($products as $key=> $product){
            //     $products_arr[$key] = new stdClass; 
            //     $products_arr[$key]->id = $product->getId();
            //     $products_arr[$key]->serialId = $product->getSerialId();
            //     $products_arr[$key]->name = $product->getName();
            //     $products_arr[$key]->warranty = $product->getWarranty();
            // }
            // print_r($products_arr); 
            $params['login'] = true;
            $params['products'] = $products;
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
        if(is_object($customer)){
            $params['login'] = true;
            $_SESSION['customerId'] = $customer->getId();
            $_SESSION['email'] = $customer->getEmail();
            $_SESSION['name'] = $customer->getName();
            $_SESSION['surname'] = $customer->getSurname();
            $_SESSION['mob_no'] = $customer->getMobNo();
            self::showView('default', $params);

        }elseif(is_string($customer)){
            $params['error'] = $customer;
            self::showView('login', $params);
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
        $customerId = filter_var($_SESSION['customerId'], FILTER_VALIDATE_INT);
        $product = new Product(id: $productId);
        $product = Product::productRegisterCustomer($product, $customerId); 
        // $params['product'] = new stdClass;
        // $params['product']->serialId = $product->getSerialId();  
        // $params['product']->name = $product->getName();  
        // $params['product']->warranty = $product->getWarranty();  
        $params['product'] = $product;
        $params['product_register'] = true;
        $params['login'] = true;
        self::showView('default', $params);
    }

}
