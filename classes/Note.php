<?php
require_once 'config/database.php';

class Note {
    private $conn;
    private $table = 'notes';
    
    // Note properties
    public $id;
    public $prospect_id;
    public $user_id;
    public $content;
    public $created_at;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }
    
    // Get notes for a prospect
    public function read_by_prospect() {
        try {
            $query = "SELECT n.id, n.prospect_id, n.user_id, n.content, n.created_at, u.username 
                      FROM " . $this->table . " n
                      LEFT JOIN users u ON n.user_id = u.id
                      WHERE n.prospect_id = :prospect_id
                      ORDER BY n.created_at DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':prospect_id', $this->prospect_id, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt;
        } catch (PDOException $e) {
            echo "Error fetching notes: " . $e->getMessage();
            return false;
        }
    }
    
    // Create note
    public function create() {
        try {
            $query = "INSERT INTO " . $this->table . " (prospect_id, user_id, content) 
                      VALUES (:prospect_id, :user_id, :content)";
            
            $stmt = $this->conn->prepare($query);
            
            // Clean data
            $this->content = htmlspecialchars(strip_tags($this->content));
            
            // Bind data
            $stmt->bindParam(':prospect_id', $this->prospect_id, PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $this->user_id, PDO::PARAM_INT);
            $stmt->bindParam(':content', $this->content, PDO::PARAM_STR);
            
            // Execute query
            if ($stmt->execute()) {
                $this->id = $this->conn->lastInsertId();
                return true;
            }
        } catch (PDOException $e) {
            echo "Error creating note: " . $e->getMessage();
        }
        
        return false;
    }
    
    // Delete note
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
            echo "Error deleting note: " . $e->getMessage();
        }
        
        return false;
    }
}
?>