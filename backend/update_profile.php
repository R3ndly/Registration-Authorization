<?php
include './connect-bd.php';

session_start();
class updateUser extends DatabaseConnection {

    public function __construct() {
        parent::__construct();
    }

    public function UpdateUser() {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST["username"];
        $phone = $_POST["phone"];
        $email = $_POST["email"];
        $old_password = $_POST["old_password"];
        $new_password = $_POST["new_password"];
        $confirm_password = $_POST["confirm_password"];

        $checkUserQuery = "SELECT * FROM users WHERE username=?";
        $request = $this->getConnection()->prepare($checkUserQuery);
        $request->bind_param("s", $_SESSION['username']);
        $request->execute();
        $result = $request->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            if (!password_verify($old_password, $user['password'])) {
                echo "Неправильный старый пароль.";
                return;
            }

            if ($new_password !== $confirm_password) {
                echo "Новый пароль и подтверждение не совпадают.";
                return;
            }

            $checkUsernameQuery = "SELECT * FROM users WHERE username=?";
            $request = $this->getConnection()->prepare($checkUsernameQuery);
            $request->bind_param("s", $username);
            $request->execute();
            $usernameResult = $request->get_result();

            if ($usernameResult->num_rows > 0 && $username !== $_SESSION['username']) {
                echo "Логин уже существует.";
                return;
            }

            $checkPhoneQuery = "SELECT * FROM users WHERE phone=?";
            $request = $this->getConnection()->prepare($checkPhoneQuery);
            $request->bind_param("s", $phone);
            $request->execute();
            $phoneResult = $request->get_result();

            if ($phoneResult->num_rows > 0 && $phone !== $user['phone']) {
                echo "Телефон уже существует.";
                return;
            }

            $checkEmailQuery = "SELECT * FROM users WHERE email=?";
            $request = $this->getConnection()->prepare($checkEmailQuery);
            $request->bind_param("s", $email);
            $request->execute();
            $emailResult = $request->get_result();

            if ($emailResult->num_rows > 0 && $email !== $user['email']) {
                echo "Email уже существует.";
                return;
            }

            $updateQuery = "UPDATE users SET username=?, phone=?, email=?, password=? WHERE username=?";
            $request = $this->getConnection()->prepare($updateQuery);
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $request->bind_param("sssss", $username, $phone, $email, $hashed_password, $_SESSION['username']);
            $request->execute();

            if ($request->affected_rows > 0) {
                echo "Данные обновлены успешно.";
                $_SESSION['username'] = $username;
                $_SESSION['phone'] = $phone;
                $_SESSION['email'] = $email;
                header('Location: ../View/profile.php');
                exit;
            } else {
                echo "Ошибка при обновлении данных.";
            }
        } else {
            echo "Пользователь не найден.";
        }
    }
}

    public function __destruct() {
        $this->closeConnection();
    }
}

$update = new updateUser(); 
$update->UpdateUser(); 
?>
