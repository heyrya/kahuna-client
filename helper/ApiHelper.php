<?php
namespace app\kahuna\client\helper;

class ApiHelper
{
    public static function getRequestData()
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        switch($requestMethod)
        {
            case 'GET':
                return $_GET;
            case 'POST':
                return $_POST;
            default: 
                return [];
        }
    }
}