<?php
namespace app\kahuna\client\model;

// constant defined for time in seconds for a year
define("YEAR_IN_SECONDS", 31536000);

use app\kahuna\client\model\DBConnect;
use \PDO;
use \stdClass;
use \DateTime;

class Product
{

    private static $db;
    private ?int $id;
    private ?string $serialId;
    private ?string $name;
    private ?int $warranty;
    private ?int $registered;
    

    public function __construct(?int $id = 0, ?string $serialId = null, ?string $name = null, ?int $warranty = 0, ?int $registered = 0)
    {
        $this->id = $id;
        $this->serialId = $serialId;
        $this->name = $name;
        $this->warranty = $warranty;
        $this->registered = $registered;
        self::$db = DBConnect::getInstance()->getConnection();
    }

    
   
    public static function getProductsUnregistered()
    {
        self::$db = DBConnect::getInstance()->getConnection();
        $sql = "SELECT * FROM productstock WHERE registered= 0";
        $sth = self::$db->query($sql);
        $products = $sth->fetchAll(PDO::FETCH_FUNC, fn(...$fields) => new Product(...$fields));
        return $products;
    }

    public static function productRegisterCustomer(Product $product, int $customerId)
    {
        $sql = <<<SQL
        UPDATE productstock SET registered = 1 WHERE id = :productId;
        INSERT INTO customerproduct(customerId, productstockId) 
        VALUES (:customerId ,:productId);
        SQL;
        $sth = self::$db->prepare($sql);
        $sth->bindValue('productId', $product->getId());
        $sth->bindValue('customerId', $customerId);
        $sth->execute();
        $sql = "SELECT * FROM productstock WHERE id = :id";
        $sth = self::$db->prepare($sql);
        $sth->bindValue('id', $product->getId());
        $sth->execute();
        $result = $sth->fetch(PDO::FETCH_OBJ);
        $product->setName($result->name);
        $product->setSerialId($result->serialId);
        $product->setWarranty($result->warranty);
        $product->setRegistered($result->registered);
        return $product;
    }

    public static function getProduct(int $productId)
    {
        self::$db = DBConnect::getInstance()->getConnection();
        $sql = "SELECT * FROM productstock WHERE id = :productId";
        $sth = self::$db->prepare($sql);
        $sth->bindValue('productId', $productId);
        $sth->execute();
        $result = $sth->fetch(PDO::FETCH_OBJ);
        return $result;
    }
    public static function getProductsCustomer(int $customerId)
    {
        self::$db = DBConnect::getInstance()->getConnection();
        $sql = <<<SQL
        SELECT productstock.id, productstock.serialId, productstock.name, productstock.warranty, customerproduct.registrationDate
        FROM productstock
        JOIN customerproduct ON customerproduct.productstockId = productstock.id
        WHERE customerproduct.customerId = :customerId;
        SQL;
        $sth = self::$db->prepare($sql);
        $sth->bindValue('customerId', $customerId);
        $sth->execute();
        $products = $sth->fetchAll(PDO::FETCH_FUNC, function ($id, $serialId, $name, $warranty, $registrationDate){
            $registrationTime = strtotime($registrationDate);
            // get current time in seconds
            $now = time();
            $elapsedTime = $now - $registrationTime;
           
            $product = new stdClass;
            $product->id = $id;
            $product->serialId = $serialId;
            $product->name = $name;
            
            // calculate warranty period in seconds
            $warrantyTimeSeconds = $warranty * YEAR_IN_SECONDS;
    
            if($elapsedTime > $warrantyTimeSeconds){
                $product->expired = 1;
                return $product;
            }else{
                $registrationDate = new DateTime($registrationDate);
                $warrantyExpirationDate = date("Y-m-d H:i:s", $registrationTime + $warrantyTimeSeconds);
                $warrantyExpiration = new DateTime($warrantyExpirationDate);

                $diffPeriod = $warrantyExpiration->diff($registrationDate);
                $computedPeriod = $diffPeriod->format('%y years %m months %d days');
                $product->warrantyLeft = $computedPeriod;
                $product->expired = 0;
                return $product;
            }
        });
        return $products;
    }

  

    

    
   
    
    /**
     * Get the value of id
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set the value of id
     */
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of serialId
     */
    public function getSerialId(): string
    {
        return $this->serialId;
    }

    /**
     * Set the value of serialId
     */
    public function setSerialId(string $serialId): self
    {
        $this->serialId = $serialId;

        return $this;
    }

    /**
     * Get the value of name
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set the value of name
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of warranty
     */
    public function getWarranty(): int
    {
        return $this->warranty;
    }

    /**
     * Set the value of warranty
     */
    public function setWarranty(int $warranty): self
    {
        $this->warranty = $warranty;

        return $this;
    }

    /**
     * Get the value of registered
     */
    public function getRegistered(): int
    {
        return $this->registered;
    }

    /**
     * Set the value of registered
     */
    public function setRegistered(int $registered): self
    {
        $this->registered = $registered;

        return $this;
    }
}
