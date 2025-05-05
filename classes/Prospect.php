<?php
require_once 'config/database.php';

class Prospect {
    private $conn;
    private $table = 'prospects';
    
    // Prospect properties
    public $id;
    public $name;
    public $company;
    public $phone;
    public $email;
    public $status;
    public $created_at;
    public $user_id;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }
    
    // Get all prospects
    public function read($status = null, $limit = 10, $offset = 0) {
        $query = "SELECT p.id, p.name, p.company, p.phone, p.email, p.status, p.created_at, p.user_id, u.username 
                  FROM " . $this->table . " p
                  LEFT JOIN users u ON p.user_id = u.id";
        
        // Add status filter if provided
        if($status) {
            $query .= " WHERE p.status = :status";
        }
        
        $query .= " ORDER BY p.created_at DESC LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        
        if($status) {
            $stmt->bindParam(':status', $status);
        }
        
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        
        $stmt->execute();
        
        return $stmt;
    }
    
    // Get total count of prospects
    public function count($status = null) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        
        if($status) {
            $query .= " WHERE status = :status";
        }
        
        $stmt = $this->conn->prepare($query);
        
        if($status) {
            $stmt->bindParam(':status', $status);
        }
        
        $stmt->execute();
        $row = $stmt->fetch();
        
        return $row['total'];
    }
    
    // Get single prospect
    public function read_single() {
        $query = "SELECT p.id, p.name, p.company, p.phone, p.email, p.status, p.created_at, p.user_id, u.username 
                  FROM " . $this->table . " p
                  LEFT JOIN users u ON p.user_id = u.id
                  WHERE p.id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
        
        $row = $stmt->fetch();
        
        if($row) {
            $this->name = $row['name'];
            $this->company = $row['company'];
            $this->phone = $row['phone'];
            $this->email = $row['email'];
            $this->status = $row['status'];
            $this->created_at = $row['created_at'];
            $this->user_id = $row['user_id'];
            return true;
        }
        
        return false;
    }
    
    // Create prospect
    public function create() {
        $query = "INSERT INTO " . $this->table . " (name, company, phone, email, status, user_id) 
                  VALUES (:name, :company, :phone, :email, :status, :user_id)";
        
        $stmt = $this->conn->prepare($query);
        
        // Clean data
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->company = htmlspecialchars(strip_tags($this->company));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->status = htmlspecialchars(strip_tags($this->status));
        
        // Bind data
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':company', $this->company);
        $stmt->bindParam(':phone', $this->phone);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':user_id', $this->user_id);
        
        // Execute query
        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        
        // Print error if something goes wrong
        printf("Error: %s.\n", $stmt->error);
        
        return false;
    }
    
    // Update prospect
    public function update() {
        $query = "UPDATE " . $this->table . " 
                  SET name = :name, company = :company, phone = :phone, email = :email, status = :status 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        // Clean data
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->company = htmlspecialchars(strip_tags($this->company));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->id = htmlspecialchars(strip_tags($this->id));
        
        // Bind data
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':company', $this->company);
        $stmt->bindParam(':phone', $this->phone);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':id', $this->id);
        
        // Execute query
        if($stmt->execute()) {
            return true;
        }
        
        // Print error if something goes wrong
        printf("Error: %s.\n", $stmt->error);
        
        return false;
    }
    
    // Delete prospect
    public function delete() {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        
        // Clean data
        $this->id = htmlspecialchars(strip_tags($this->id));
        
        // Bind data
        $stmt->bindParam(':id', $this->id);
        
        // Execute query
        if($stmt->execute()) {
            return true;
        }
        
        // Print error if something goes wrong
        printf("Error: %s.\n", $stmt->error);
        
        return false;
    }
}
?>
