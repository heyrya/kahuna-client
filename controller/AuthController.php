<?php
namespace app\kahuna\client\controller;

class AuthController
{
    public static function logout():void
    {
        foreach(array_keys($_SESSION) as $session_key){
            unset($_SESSION[$session_key]);
        }
        session_destroy();
    }
}