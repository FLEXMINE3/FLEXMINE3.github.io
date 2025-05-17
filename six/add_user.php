<?php
require_once 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['logged_in']) || empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// Проверка прав администратора

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['login'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;
    $application_id = (int)$_POST['application_id'];

    // Получить данные из таблицы application
    $stmt = $conn->prepare("SELECT name, phone, email, dob, gender, bio FROM application WHERE id = ?");
    $stmt->bind_param("i", $application_id);
    $stmt->execute();
    $app_result = $stmt->get_result();
    $app_data = $app_result->fetch_assoc();
    $stmt->close();

    if (!$app_data) {
        die("Заявка с таким ID не найдена.");
    }

    // Вставить пользователя с данными из application
    $stmt = $conn->prepare("INSERT INTO users (application_id, login, password_hash, name, phone, email, dob, gender, bio, is_admin)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssssssi",
        $application_id,
        $login,
        $password,
        $app_data['name'],
        $app_data['phone'],
        $app_data['email'],
        $app_data['dob'],
        $app_data['gender'],
        $app_data['bio'],
        $is_admin
    );
    $stmt->execute();
    $stmt->close();

    header('Location: admin_panel.php');
    exit;
}
?>

<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><title>Добавить пользователя</title></head>
<style>
body {  
            background-image:url("five.jpg");
            background-size:cover;
        }
        
    </style>

<body>
<h2 style="color:white">Добавить пользователя</h2>
<form style="color:white" method="post">
    Login: <input type="text" name="login" required><br>
    Password: <input type="password" name="password" required><br>
    Application ID: <input type="number" name="application_id" required><br>
    Is Admin: <input type="checkbox" name="is_admin"><br>
    <input type="submit" value="Создать">
</form>
</body>
</html>