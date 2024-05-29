<?php
namespace app\kahuna\client\controller;

use app\kahuna\client\model\DBConnect;
use \PDO;


class AgentController
{
    private const BASE_URL = "http://kahuna-api.localhost";
    private static $db;


    public static function req(string $method, string $endpoint, mixed $data)
    {
        $url = self::BASE_URL . $endpoint;

        // initiate curl session
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        switch($method){
            case 'POST':
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                break;
            default:
                break;
        }

        // Headers
        if(isset($_SESSION['api_user']) && isset($_SESSION['api_token'])){
            curl_setopt($curl, CURLOPT_HTTPHEADER,[
                "X-Api-Key: {$_SESSION['api_token']}",
                "X-Api-User: {$_SESSION['api_user']}"
            ]);
        }elseif(isset($data['api_token']) && isset($data['api_user'])){
            curl_setopt($curl, CURLOPT_HTTPHEADER, [
                "X-Api-Key: {$data['api_token']}",
                "X-Api-User: {$data['api_user']}"
            ]);
        }

        $response = curl_exec($curl);
        curl_close($curl);
        return $response;

    }

    public static function checkToken(): bool
    {
        if(!isset($_SESSION['api_token']) || !isset($_SESSION['api_user'])){
            return false;
        }

        $input = [
            'api_user' => $_SESSION['api_user'],
            'api_token' => $_SESSION['api_token']
        ];

        $result = json_decode(self::req('GET', '/agent/token', $input));
        return $result->data->valid ?? false;
        
    }

    public static function getCustomerProductInfo(int $customerproductId)
    {
        self::$db = DBConnect::getInstance()->getConnection();
        $sql = <<<SQL
        SELECT customer.name, customer.surname, customer.email, productstock.serialId, productstock.name AS 'productName'
        FROM customerproduct 
        JOIN customer ON customerproduct.customerId = customer.id 
        JOIN productstock ON customerproduct.productstockId = productstock.id 
        WHERE customerproduct.id = :customerproductId; 
        SQL;
        $sth = self::$db->prepare($sql);
        $sth->bindValue('customerproductId', $customerproductId);
        $sth->execute();
        $result = $sth->fetch(PDO::FETCH_ASSOC);
        return $result;
    }

}