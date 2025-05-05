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
        try {
            $query = "SELECT p.id, p.name, p.company, p.phone, p.email, p.status, p.created_at, p.user_id, u.username 
                      FROM " . $this->table . " p
                      LEFT JOIN users u ON p.user_id = u.id";
            
            // Add status filter if provided
            if ($status) {
                $query .= " WHERE p.status = :status";
            }
            
            $query .= " ORDER BY p.created_at DESC LIMIT :limit OFFSET :offset";
            
            $stmt = $this->conn->prepare($query);
            
            if ($status) {
                $stmt->bindParam(':status', $status, PDO::PARAM_STR);
            }
            
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            
            $stmt->execute();
            
            return $stmt;
        } catch (PDOException $e) {
            echo "Error fetching prospects: " . $e->getMessage();
            return false;
        }
    }
    
    // Get total count of prospects
    public function count($status = null) {
        try {
            $query = "SELECT COUNT(*) as total FROM " . $this->table;
            
            if ($status) {
                $query .= " WHERE status = :status";
            }
            
            $stmt = $this->conn->prepare($query);
            
            if ($status) {
                $stmt->bindParam(':status', $status, PDO::PARAM_STR);
            }
            
            $stmt->execute();
            $row = $stmt->fetch();
            
            return $row['total'];
        } catch (PDOException $e) {
            echo "Error counting prospects: " . $e->getMessage();
            return 0;
        }
    }
    
    // Get single prospect
    public function read_single() {
        try {
            $query = "SELECT p.id, p.name, p.company, p.phone, p.email, p.status, p.created_at, p.user_id, u.username 
                      FROM " . $this->table . " p
                      LEFT JOIN users u ON p.user_id = u.id
                      WHERE p.id = :id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
            $stmt->execute();
            
            $row = $stmt->fetch();
            
            if ($row) {
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
        } catch (PDOException $e) {
            echo "Error fetching prospect: " . $e->getMessage();
            return false;
        }
    }
    
    // Create prospect
    public function create() {
        try {
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
            $stmt->bindParam(':name', $this->name, PDO::PARAM_STR);
            $stmt->bindParam(':company', $this->company, PDO::PARAM_STR);
            $stmt->bindParam(':phone', $this->phone, PDO::PARAM_STR);
            $stmt->bindParam(':email', $this->email, PDO::PARAM_STR);
            $stmt->bindParam(':status', $this->status, PDO::PARAM_STR);
            $stmt->bindParam(':user_id', $this->user_id, PDO::PARAM_INT);
            
            // Execute query
            if ($stmt->execute()) {
                $this->id = $this->conn->lastInsertId();
                return true;
            }
        } catch (PDOException $e) {
            echo "Error creating prospect: " . $e->getMessage();
        }
        
        return false;
    }
    
    // Update prospect
    public function update() {
        try {
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
            $stmt->bindParam(':name', $this->name, PDO::PARAM_STR);
            $stmt->bindParam(':company', $this->company, PDO::PARAM_STR);
            $stmt->bindParam(':phone', $this->phone, PDO::PARAM_STR);
            $stmt->bindParam(':email', $this->email, PDO::PARAM_STR);
            $stmt->bindParam(':status', $this->status, PDO::PARAM_STR);
            $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
            
            // Execute query
            if ($stmt->execute()) {
                return true;
            }
        } catch (PDOException $e) {
            echo "Error updating prospect: " . $e->getMessage();
        }
        
        return false;
    }
    
    // Delete prospect
    public function delete() {
        try {
            $query = "DELETE FROM " . $this->table . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            
            // Clean data
            $this->id = htmlspecialchars(strip_tags($this->id));
            
            // Bind data
            $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
            
            // Execute query
            if ($stmt->execute()) {
                return true;
            }
        } catch (PDOException $e) {
            echo "Error deleting prospect: " . $e->getMessage();
        }
        
        return false;
    }
}
?>