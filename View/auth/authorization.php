<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Тестовое задание - страница авторизации</title>
    <link rel="stylesheet" href="../resources/css/style.css">
    <script src="https://smartcaptcha.yandexcloud.net/captcha.js" defer></script>
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
    <h1>Авторизация</h1><br>
    <form action="./authorization.php" method="post">
        
        <label for="address">Телефон или Email:</label>
        <input type="text" id="address" name="address" required><br><br>
        
        
        <label for="password">Пароль:</label>
        <input type="password" id="password" name="password" required><br><br>

        <div id="captcha-container" class="smart-captcha" style="height: 100px" data-sitekey="ysc1_IfFdL9iEn8Jag4xHJbAFNa9qRQnFQUUROS2pcA90f5e7f601">
            <input type="hidden" name="smart-token" value="">
        </div><br>
        
        <button type="submit">Авторизироваться</button>
    </form>
</body>
</html>


<?php
include '../../backend/connect-bd.php';

class Authorization extends DatabaseConnection {

    public function __construct() {
        parent::__construct();
    }

    public function authorizationUser() {

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $address = $_POST["address"];
            $password = $_POST["password"];
            $token = $_POST["smart-token"];

            $checkUserQuery = "SELECT * FROM users WHERE email=? OR phone=?";
            $request = $this->getConnection()->prepare($checkUserQuery);

            if (!$request) {
                die("Ошибка в запросе:" . $this->getConnection()->error);
            }

            $request->bind_param("ss", $address, $address);
            $request->execute();
            $result = $request->get_result();

            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();

                if (password_verify($password, $user['password'])) {
                    echo $user['username'] . ", добро пожаловать";

                    session_start();
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['authorized'] = true;
                    header('Location: ../profile.php');
                    exit;  
                } else {
                    echo "Неправильный пароль.";
                }
            } else {
                echo "Пользователь не найден.";
            }
        }
    }
    private function check_captcha($token) {
       // Замените на ваш ключ сервера, а то GitHub не пропускает

        $ch = curl_init();
        $args = http_build_query([
            "secret" => SMARTCAPTCHA_SERVER_KEY,
            "token" => $token,
            "ip" => $_SERVER['REMOTE_ADDR'],
        ]);
        curl_setopt($ch, CURLOPT_URL, "https://smartcaptcha.yandexcloud.net/validate?$args");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1);

        $server_output = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpcode !== 200) {
            echo "Allow access due to an error: code=$httpcode; message=$server_output\n";
            return true;
        }
        $resp = json_decode($server_output);
        return $resp->status === "ok";
    }

    public function __destruct() {
        $this->closeConnection();
    }
}

$authorization = new Authorization();
$authorization->authorizationUser();
?>
