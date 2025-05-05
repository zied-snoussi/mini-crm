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
        $query = "SELECT n.id, n.prospect_id, n.user_id, n.content, n.created_at, u.username 
                  FROM " . $this->table . " n
                  LEFT JOIN users u ON n.user_id = u.id
                  WHERE n.prospect_id = :prospect_id
                  ORDER BY n.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':prospect_id', $this->prospect_id);
        $stmt->execute();
        
        return $stmt;
    }
    
    // Create note
    public function create() {
        $query = "INSERT INTO " . $this->table . " (prospect_id, user_id, content) 
                  VALUES (:prospect_id, :user_id, :content)";
        
        $stmt = $this->conn->prepare($query);
        
        // Clean data
        $this->content = htmlspecialchars(strip_tags($this->content));
        
        // Bind data
        $stmt->bindParam(':prospect_id', $this->prospect_id);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':content', $this->content);
        
        // Execute query
        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        
        // Print error if something goes wrong
        printf("Error: %s.\n", $stmt->error);
        
        return false;
    }
    
    // Delete note
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
