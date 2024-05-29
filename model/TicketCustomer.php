<?php
namespace app\kahuna\client\model;

use app\kahuna\client\model\DBConnect;
use \PDO;

class TicketCustomer
{

    private static $db;
    private ?string $ticketNo;
    private ?string $ticketDate;
    private ?string $ticketMessage;
    private ?string $ticketReply;
    private ?int $agentId;
    private ?int $customerproductId;

    public function __construct(string $ticketMessage, ?string $ticketReply = null, ?int $agentId = null, ?int $customerproductId = null)
    {
        $this->ticketNo = uniqid();
        $this->ticketDate = date('Y-m-d H:i:s');
        $this->ticketMessage = $ticketMessage;
        $this->ticketReply = $ticketReply;
        $this->agentId = $agentId;
        $this->customerproductId = $customerproductId;
        self::$db = DBConnect::getInstance()->getConnection();
    }

    public static function submitTickcet(TicketCustomer $ticket, int $customerId, int $productId)
    {
        $sql = "SELECT agent.id FROM agent";
        $sth = self::$db->query($sql);
        $result = $sth->fetchAll(PDO::FETCH_FUNC, function($id){
            return $arr[] = $id;
        });
        $selectAgent = rand(0, count($result) - 1);
        $agentId = $result[$selectAgent];
        $ticket->setAgentId($agentId);

        $sql = "SELECT customerproduct.id FROM customerproduct WHERE customerproduct.customerId = :customerId AND customerproduct.productstockId = :productId";
        $sth = self::$db->prepare($sql);
        $sth->bindValue('customerId', $customerId);
        $sth->bindValue('productId', $productId);
        $sth->execute();
        $result = $sth->fetch(PDO::FETCH_ASSOC);
        $ticket->setCustomerproductId($result['id']);

        $sql = "INSERT INTO ticket (ticketNo, ticketDate, ticketMessage, ticketReply, agentId, customerproductId) VALUES (:ticketNo, :ticketDate, :ticketMessage, :ticketReply, :agentId, :customerproductId)";
        $sth = self::$db->prepare($sql);
        $sth->bindValue('ticketNo', $ticket->getTicketNo());
        $sth->bindValue('ticketDate', $ticket->getTicketDate());
        $sth->bindValue('ticketMessage', $ticket->getTicketMessage());
        $sth->bindValue('ticketReply', $ticket->getTicketReply());
        $sth->bindValue('agentId', $ticket->getAgentId());
        $sth->bindValue('customerproductId', $ticket->getCustomerproductId());
        $sth->execute();

        if($sth->rowCount() === 1){
            return $ticket;
        } 

    }

    public static function checkTicketSubmission(int $productId)
    {
        self::$db = DBConnect::getInstance()->getConnection();
        $sql = "SELECT customerproduct.id FROM customerproduct WHERE customerproduct.productstockId = :productId";
        $sth = self::$db->prepare($sql);
        $sth->bindValue('productId', $productId);
        $sth->execute();
        $result = $sth->fetch(PDO::FETCH_ASSOC);
        $customerproductId = $result['id'];
        $sql = "SELECT ticket.ticketNo FROM ticket WHERE ticket.customerproductId = :customerproductId";
        $sth = self::$db->prepare($sql);
        $sth->bindValue('customerproductId', $customerproductId);
        $sth->execute();
        if($sth->rowCount() === 1){            
            $result = $sth->fetch(PDO::FETCH_ASSOC);
            return $result['ticketNo'];        
        }else{
            return false;
        }

    }






    /**
     * Get the value of ticketNo
     */
    public function getTicketNo(): ?string
    {
        return $this->ticketNo;
    }

    /**
     * Set the value of ticketNo
     */
    public function setTicketNo(?string $ticketNo): self
    {
        $this->ticketNo = $ticketNo;

        return $this;
    }

    /**
     * Get the value of ticketDate
     */
    public function getTicketDate(): ?string
    {
        return $this->ticketDate;
    }

    /**
     * Set the value of ticketDate
     */
    public function setTicketDate(?string $ticketDate): self
    {
        $this->ticketDate = $ticketDate;

        return $this;
    }

    /**
     * Get the value of ticketMessage
     */
    public function getTicketMessage(): ?string
    {
        return $this->ticketMessage;
    }

    /**
     * Set the value of ticketMessage
     */
    public function setTicketMessage(?string $ticketMessage): self
    {
        $this->ticketMessage = $ticketMessage;

        return $this;
    }

    /**
     * Get the value of ticketReply
     */
    public function getTicketReply(): ?string
    {
        return $this->ticketReply;
    }

    /**
     * Set the value of ticketReply
     */
    public function setTicketReply(?string $ticketReply): self
    {
        $this->ticketReply = $ticketReply;

        return $this;
    }

    /**
     * Get the value of agentId
     */
    public function getAgentId(): ?int
    {
        return $this->agentId;
    }

    /**
     * Set the value of agentId
     */
    public function setAgentId(?int $agentId): self
    {
        $this->agentId = $agentId;

        return $this;
    }

    /**
     * Get the value of customerproductId
     */
    public function getCustomerproductId(): ?int
    {
        return $this->customerproductId;
    }

    /**
     * Set the value of customerproductId
     */
    public function setCustomerproductId(?int $customerproductId): self
    {
        $this->customerproductId = $customerproductId;

        return $this;
    }
}