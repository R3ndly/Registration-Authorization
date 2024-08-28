<?php
class DatabaseConnection {
    private $conn;

    protected function __construct() {
        $this->conn = new mysqli("localhost", "root", "Q1qqqqqq", "test-task");
        
        if ($this->conn->connect_error) {
            die("Ошибка подключения: " . $this->conn->connect_error);
        }
    }

    protected function getConnection() {
        return $this->conn;
    }

    protected function closeConnection() {
        $this->conn->close();
    }
}
?>
