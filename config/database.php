<?php
require_once 'env.php';

class Database {
    private $host;
    private $username;
    private $password;
    private $database;
    private $conn;

    public function __construct() {
        // Load environment variables if not already loaded
        if (empty(Env::get('DB_HOST'))) {
            Env::load('../.env');
        }
        
        // Get database configuration from environment variables
        $this->host = Env::get('DB_HOST', 'localhost');
        $this->username = Env::get('DB_USERNAME', 'root');
        $this->password = Env::get('DB_PASSWORD', '');
        $this->database = Env::get('DB_DATABASE', 'mini_crm');
    }

    public function connect() {
        $this->conn = null;
        
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->database,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            echo "Connection Error: " . $e->getMessage();
        }
        
        return $this->conn;
    }
}
?>
