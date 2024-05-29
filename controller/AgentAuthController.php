<?php
namespace app\kahuna\client\controller;


class AgentAuthController extends AgentController
{
    public static function login($params, $data)
    {
        $input = [
            'email' => $data['email'],
            'password' => $data['password']
        ];
        return json_decode(self::req('POST', '/agent/login', $input));
    }

    public static function logout()
    {
        $_SESSION['api_token'] = null;
        $_SESSION['api_user'] = null;
        session_destroy();
    }
}