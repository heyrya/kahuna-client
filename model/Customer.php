<?php
namespace app\kahuna\client\model;

use app\kahuna\api\model\DBConnect;
use \PDO;
class Customer
{
    private int $id;
    private ?string $name;
    private ?string $surname;
    private ?string $mob_no;
    private string $email;
    private string $password;
    private string $accessLevel = 'user';
    private static $db;

    public function __construct(?string $name = null, ?string $surname = null, ?string $mob_no = null , ?string $email = null, ?string $password = null, ?string $accessLevel = 'user', ?int $id = 0)
    {
        $this->name = $name;
        $this->surname = $surname;
        $this->email = $email;
        $this->password = $password;
        $this->accessLevel = $accessLevel;
        $this->id = $id;
        self::$db = DBConnect::getInstance()->getConnection();
    }

    public static function register(Customer $customer)
    {
        $password = password_hash($customer->getPassword(), PASSWORD_DEFAULT);
        $sql = "INSERT INTO customer (name, surname, mob_no, email, password, accessLevel) VALUES (:name, :surname, :mob_no, :email, :password, :accessLevel)";
        $sth = self::$db->prepare($sql);
        $sth->bindValue('name', $customer->getName());
        $sth->bindValue('surname', $customer->getSurname());
        $sth->bindValue('mob_no', $customer->getMobNo());
        $sth->bindValue('email', $customer->getEmail());
        $sth->bindValue('password', $password);
        $sth->bindValue('accessLevel', $customer->getAccessLevel());
        $sth->execute();

        if($sth->rowCount() > 0){
            $customer->setId(self::$db->lastInsertId());
        }

        return $customer;
    }

    public static function authenticate(Customer $customer)
    {
        $sql = "SELECT * FROM customer WHERE email=:email";
        $sth = self::$db->prepare($sql);
        $sth->bindValue('email', $customer->getEmail());
        $sth->execute();
        $result = $sth->fetch(PDO::FETCH_OBJ);
        if($result && password_verify($customer->getPassword(), $result->password)){   
            return new Customer(
                name: $result->name,
                surname:$result->surname,
                mob_no: $result->mob_no,
                email: $result->email,
                password: $result->password,
                id: $result->id
            );
        }

        return null;
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
     * Get the value of name
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set the value of name
     */
    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of surname
     */
    public function getSurname(): ?string
    {
        return $this->surname;
    }

    /**
     * Set the value of surname
     */
    public function setSurname(?string $surname): self
    {
        $this->surname = $surname;

        return $this;
    }

    /**
     * Get the value of mob_no
     */
    public function getMobNo(): ?string
    {
        return $this->mob_no;
    }

    /**
     * Set the value of mob_no
     */
    public function setMobNo(?string $mob_no): self
    {
        $this->mob_no = $mob_no;

        return $this;
    }

    /**
     * Get the value of email
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Set the value of email
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the value of password
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * Set the value of password
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get the value of accessLevel
     */
    public function getAccessLevel(): string
    {
        return $this->accessLevel;
    }

    /**
     * Set the value of accessLevel
     */
    public function setAccessLevel(string $accessLevel): self
    {
        $this->accessLevel = $accessLevel;

        return $this;
    }
}