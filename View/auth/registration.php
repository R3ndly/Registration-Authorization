<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Тестовое задание - страница регистрации</title>
    <link rel="stylesheet" href="../resources/css/style.css">
</head>
<body>
<header class="header">
        <h1><a href="./home.html" class="logo">TeSk</a></h1>

        <div class="container">
            <nav class="header__list">
                <ul class="header__link">
                    <li class="header__item"><a href="../about.html">О нас</a></li>
                    <li class="header__item"><a href="../auth/authorization.php">Войти</a></li>
                    <li class="header__item"><a href="../auth/registration.php">Зарегистрироваться</a></li>
                    <li class="header__item"><a href="../profile.php">Профиль</a></li>
                </ul>
            </nav>
        </div>
    </header><br><br>
    <h1>РЕГИСТРАЦИЯ</h1><br>
    <form action="./registration.php" method="post">
        
    <label for="login">Имя пользователя:</label>
    <input type="text" id="login" name="login" required><br><br>

    <label for="phone">Телефон:</label>
    <input type="number" id="phone" name="phone" required><br><br>

    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required><br><br>

    <label for="password">Пароль:</label>
    <input type="password" id="password" name="password" required><br><br>

    <label for="Repeat-password">Повторите пароль:</label>
    <input type="Repeat-password" id="Repeat-password" name="Repeat-password" required><br><br>

    <button type="submit">Зарегистрироваться</button>
</form>
</body>
</html>


<?php
include '../../backend/connect-bd.php';

class Registration extends DatabaseConnection {
     
    public function __construct() {
        parent::__construct();
    }

    public function registerUser() {

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $username = $_POST['login'];
            $phone = $_POST['phone'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            $RepeatPassword = $_POST['Repeat-password'];

            if($password !== $RepeatPassword) {
                echo "<h2>Пароли не совпадают!</h2>";
            } else {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            }

            $checkUserQuery = "SELECT * FROM users WHERE phone=? OR email=? OR username=?";
            $request = $this->getConnection()->prepare($checkUserQuery);

            if(!$request) {
                die("ошибка в запросе:" . $this->getConnection()->error);
            }
            
            $request->bind_param("iss", $phone, $email, $username);
            $request->execute();
            $result = $request->get_result();

            if ($result->num_rows > 0) {
                echo "Пользователь с таким телефоном ИЛИ email ИЛИ логином уже существует.";
            } else {
                $sql = "INSERT INTO users (username, email, password, phone) VALUES (?, ?, ?, ?)";
                $request = $this->getConnection()->prepare($sql);

                if(!$request) {
                    die("ошибка в запросе:" . $this->getConnection()->error);
                }
                $request->bind_param("sssi", $username, $email, $hashedPassword, $phone);

                if ($request->execute()) {
                    echo "Регистрация успешна!";
                } else {
                    echo "Ошибка: " . $request->error;
                }
            }
        }
    }
        public function __destruct() {
            $this->closeConnection();
        }
}

$registration = new Registration(); 
$registration->registerUser();   
?>


