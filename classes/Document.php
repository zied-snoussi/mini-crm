<?php
require_once 'config/database.php';

class Document {
    private $conn;
    private $table = 'documents';
    
    // Document properties
    public $id;
    public $prospect_id;
    public $user_id;
    public $filename;
    public $original_filename;
    public $file_type;
    public $created_at;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }
    
    // Get documents for a prospect
    public function read_by_prospect() {
        $query = "SELECT d.id, d.prospect_id, d.user_id, d.filename, d.original_filename, d.file_type, d.created_at, u.username 
                  FROM " . $this->table . " d
                  LEFT JOIN users u ON d.user_id = u.id
                  WHERE d.prospect_id = :prospect_id
                  ORDER BY d.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':prospect_id', $this->prospect_id);
        $stmt->execute();
        
        return $stmt;
    }
    
    // Create document
    public function create() {
        $query = "INSERT INTO " . $this->table . " (prospect_id, user_id, filename, original_filename, file_type) 
                  VALUES (:prospect_id, :user_id, :filename, :original_filename, :file_type)";
        
        $stmt = $this->conn->prepare($query);
        
        // Clean data
        $this->filename = htmlspecialchars(strip_tags($this->filename));
        $this->original_filename = htmlspecialchars(strip_tags($this->original_filename));
        $this->file_type = htmlspecialchars(strip_tags($this->file_type));
        
        // Bind data
        $stmt->bindParam(':prospect_id', $this->prospect_id);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':filename', $this->filename);
        $stmt->bindParam(':original_filename', $this->original_filename);
        $stmt->bindParam(':file_type', $this->file_type);
        
        // Execute query
        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        
        // Print error if something goes wrong
        printf("Error: %s.\n", $stmt->error);
        
        return false;
    }
    
    // Delete document
    public function delete() {
        // First get the filename to delete the file
        $query = "SELECT filename FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
        
        $row = $stmt->fetch();
        
        if($row) {
            $this->filename = $row['filename'];
            
            // Delete from database
            $query = "DELETE FROM " . $this->table . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $this->id);
            
            // Execute query
            if($stmt->execute()) {
                // Delete file from uploads directory
                $file_path = '../uploads/' . $this->filename;
                if(file_exists($file_path)) {
                    unlink($file_path);
                }
                return true;
            }
        }
        
        return false;
    }
}
?>
