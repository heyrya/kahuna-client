<?php
namespace app\kahuna\client\controller;

use app\kahuna\client\model\Product;
use app\kahuna\client\model\Ticket;
use \Twig\Environment;
use \app\kahuna\client\model\Customer;
use app\kahuna\client\model\TicketCustomer;
use \stdClass;

class RouteController
{
    private static ?Environment $twig_customer = null;

    public static function showView(string $view, ?array $params = []): void {
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
    
    // Products list per Customer View
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

    // Ticket View
    public static function viewProductTicketCustomer(array $params, array $data):void
    {   
        if(isset($_SESSION['customerId'])){
            $product = Product::getProduct($params['product_id']);
            $params['login'] = true;
            $params['product'] = $product;
            (isset($data['warrantyExpired'])) ? $params['warrantyExpired'] = $data['warrantyExpired'] : $params['warrantyExpired'] = 0;
            $submittedTicket = TicketCustomer::checkTicketSubmission($product->id);
            ($submittedTicket) ? $params['ticketSubmitted'] = true : $params['ticketSubmitted'] = false;
            self::showView('product-info-ticket', $params);    
        }else{
            self::showView('default', $params);
        }
    }

    // Product Registration View
    public static function viewRegisterProductCustomer(array $params, array $data):void
    {
        if(isset($_SESSION['customerId'])){
            $products = Product::getProductsUnregistered(); 
            $params['login'] = true;
            $params['products'] = $products;
            self::showView('product-register', $params);
        }else{
            self::showView('default', $params);
        }
    }


    /**-------------------- */

    /**Customer actions */

    // Registration Form submission action
    public static function actionRegisterCustomer(array $params, array $data): void
    {
        $result = Customer::registrationValidation($data);
        if(is_array($result) || is_string($result)){
            (is_string($result)) ? $result = [$result] : $result;
            self::showView('registration-fail', ["errors"=>$result]);
        }else{
            self::showView('default', ["register"=>true]);
        }
    }

    // Sign In Action
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
            $_SESSION['ticketNo'] = [];
            self::showView('default', $params);

        }elseif(is_string($customer)){
            $params['error'] = $customer;
            self::showView('login', $params);
        }
    }

    // Sign Out Action
    public static function actionLogoutCustomer(array $params, array $data): void
    {
        AuthController::logout();
        $params['login'] = false;
        self::showView('default', $params);
    }

    // Product Registration Action
    public static function actionRegisterProductCustomer(array $params, array $data): void
    {
        $productId = filter_input(INPUT_POST, 'product_register_id', FILTER_VALIDATE_INT);
        $customerId = filter_var($_SESSION['customerId'], FILTER_VALIDATE_INT);
        $product = new Product(id: $productId);
        $product = Product::productRegisterCustomer($product, $customerId); 
        $params['product'] = $product;
        $params['product_register'] = true;
        $params['login'] = true;
        self::showView('default', $params);
    }

    // Ticket Submission Action
    public static function actionTicketCustomer(array $params, array $data): void
    {
        $customerId = filter_var($_SESSION['customerId'], FILTER_VALIDATE_INT);
        $productId = filter_var($data['productId'], FILTER_VALIDATE_INT);
        $ticket = new TicketCustomer(ticketMessage: $data['ticket_message']);
        $getTicket = TicketCustomer::submitTickcet($ticket, $customerId, $productId);
        $_SESSION['ticketNo'][] = $getTicket->getTicketNo();
        $params['login'] = true;
        $params['ticketSubmission'] = true;
        $params['ticketNo'] = $getTicket->getTicketNo();
        self::showView('default', $params);
    }

}
