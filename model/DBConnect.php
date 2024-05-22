<?php
namespace app\kahuna\client\model;

use \PDO;

class DBConnect
{
    private static $singleton = null;
    private $dbh;


    private function __construct()
    {
        $env = parse_ini_file($_SERVER['DOCUMENT_ROOT'].'/.env');
        $this->dbh = new PDO(
            "mysql:host=localhost;dbname=kahuna_db",
            $env['DB_User'],
            $env['DB_Pass'],
            array(PDO::ATTR_PERSISTENT => true)
        );
    }

    public static function getInstance()
    {
        self::$singleton = self::$singleton ?? new DBConnect();
        return self::$singleton;
    }

    public function getConnection()
    {
        return $this->dbh;
    }
}