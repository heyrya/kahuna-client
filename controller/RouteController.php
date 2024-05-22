<?php
namespace app\kahuna\client\controller;

use \Twig\Environment;

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
        self::showView('default', $params);
    }


    /**-------------------- */

    /**Customer actions */

    
}
