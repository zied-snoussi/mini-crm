<?php
require_once 'config/database.php';

class User
{
    private $conn;
    private $table = 'users';

    // User properties
    public $id;
    public $username;
    public $email;
    public $password;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->connect();
    }

    // Login user
    public function login($username, $password)
    {
        $query = "SELECT id, username, email, password FROM " . $this->table . " WHERE username = :username";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch();
            $this->id = $row['id'];
            $this->username = $row['username'];
            $this->email = $row['email'];
            $this->password = $row['password'];
            // Verify password
            if (password_verify($password, $this->password)) {
                echo "\nPassword verified successfully.\n";
                return true;
            } else {
                echo "\nPassword verification failed.\n";
            }
        } else {
            echo "\nNo user found with the provided username.\n";
        }

        return false;
    }

    // Get user by ID
    public function read_single()
    {
        $query = "SELECT id, username, email FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        $row = $stmt->fetch();

        $this->username = $row['username'];
        $this->email = $row['email'];
    }

    // Create user
    public function create()
    {
        $query = "INSERT INTO " . $this->table . " (username, email, password) VALUES (:username, :email, :password)";
        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->password = htmlspecialchars(strip_tags($this->password));

        // Hash password
        $password_hash = password_hash($this->password, PASSWORD_BCRYPT);

        // Bind data
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password', $password_hash);

        // Execute query
        if ($stmt->execute()) {
            return true;
        }

        // Print error if something goes wrong
        printf("Error: %s.\n", $stmt->error);

        return false;
    }
}
?>