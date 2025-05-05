<?php
require_once 'config/database.php';

try {
    $database = new Database();
    $conn = $database->connect();
    echo "Database connection successful!";
} catch (Exception $e) {
    echo "Database connection failed: " . $e->getMessage();
}
?>