<?php
namespace app\kahuna\client\controller;

use app\kahuna\client\controller\AgentController;
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

    public static function viewDefaultAgent(array $params, array $data):void
    {
        self::showView('default', $params);
    }
}