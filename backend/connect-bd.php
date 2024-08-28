<?php //Вот эти данные нужно бы скрыть - добавить в .gitignore
class DatabaseConnection {
    private $conn;

    protected function __construct() {
        $this->conn = new mysqli("localhost", "root", "Q1qqqqqq", "test-task"); //скрыть эти данные нужно, так как могут увидить данные для подключения к нашей БД
        //Но я всё это оставил чтобы вы всё видели.
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
