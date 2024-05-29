<?php
namespace app\kahuna\client;

session_start();

include "vendor/autoload.php";

use app\kahuna\client\helper\ApiHelper;
use app\kahuna\client\controller\RouteController;
use app\kahuna\client\controller\AgentRouteController;

use \Twig\Loader\FilesystemLoader;
use \Twig\Environment;
use \AltoRouter;

// Twig Envirnment set for the customer
$loader_customer = new FilesystemLoader("templates/customer");
$twig_customer = new Environment($loader_customer, ['debug' => true]);
$twig_customer->addExtension(new \Twig\Extension\DebugExtension());
RouteController::setEnvironment($twig_customer);

// Twig envornment set for the Agent
$loader_agent = new FilesystemLoader("templates/agent");
$twig_agent = new Environment($loader_agent, ['debug' => true]);
$twig_agent->addExtension(new \Twig\Extension\DebugExtension());
AgentRouteController::setEnvironment($twig_agent);

$router = new AltoRouter();
/**View Routes - Customer---------------------- */
$router->map('GET', '/', "RouteController#viewDefaultCustomer", "view_default");
$router->map('GET', '/customer/login', "RouteController#viewLoginCustomer", "view_login_customer");
$router->map('GET', '/customer/register', "RouteController#viewRegisterCustomer", "view_register_customer");
$router->map('GET', '/customer/products', "RouteController#viewProductsCustomer", "view_products_customer");
$router->map('GET', '/customer/products/[i:product_id]', "RouteController#viewProductTicketCustomer", "view_product_ticket_customer");
$router->map('GET', '/customer/register-product', "RouteController#viewRegisterProductCustomer", "view_register-product_customer");
$router->map('GET', '/customer/account', "RouteController#viewAccountCustomer", "view_account_customer");
/**---------------------- */

/**Action Routes  - Customer---------------------- */
$router->map('POST', "/customer/action/login", "RouteController#actionLoginCustomer", 'action_login_customer');
$router->map('GET',"/customer/action/logout", "RouteController#actionLogoutCustomer", 'action_logout_customer');
$router->map('POST',"/customer/action/register", "RouteController#actionRegisterCustomer", 'action_register_customer');
$router->map('POST',"/customer/action/register-product", "RouteController#actionRegisterProductCustomer", 'action_register-product_customer');
$router->map('POST',"/customer/action/ticket", "RouteController#actionTicketCustomer", 'action_ticket_customer');
/**---------------------- */

/**View Routes - Agent---------------------- */
$router->map('GET', '/agent', "AgentRouteController#viewDefaultAgent", "view_default_agent");
$router->map('GET', '/agent/login', "AgentRouteController#viewLoginAgent", "view_login_agent");
$router->map('GET', '/agent/create-product', "AgentRouteController#viewCreateProductAgent", "view_create_product_agent");
$router->map('GET', '/agent/tickets', "AgentRouteController#viewTicketsAgent", "view_tickets_agent");
$router->map('GET', '/agent/tickets/[i:ticket_id]', "AgentRouteController#viewSingleTicketAgent", "view_single_ticket_agent");
/**---------------------- */

/**View Routes - Agent---------------------- */
$router->map('POST', '/agent/action/login', 'AgentRouteController#actionLoginAgent', 'action_login_agent');
$router->map('GET', '/agent/action/logout', 'AgentRouteController#actionLogoutAgent', 'action_logout_agent');
$router->map('POST', '/agent/action/create-product', 'AgentRouteController#actionCreateProductAgent', 'action_create_product_agent');
$router->map('POST', '/agent/action/ticket', 'AgentRouteController#actionTicketAgent', 'action_ticket_agent');
/**---------------------- */
$match = $router->match();
if(is_array($match)){
    $target = explode('#', $match['target']);
	$class = $target[0];
	$action = $target[1];
	$params = $match['params'];
    $requestData =  ApiHelper::getRequestData();
    call_user_func(__NAMESPACE__."\controller\\$class::$action", $params, $requestData);
}else{

}