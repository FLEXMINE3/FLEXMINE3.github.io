<?php
require_once 'config.php';

// Получаем логин из формы
$login = $_POST['login'];
$password = $_POST['password'];

// Подготовленный запрос к базе данных для поиска пользователя по логину
$stmt = $conn->prepare("SELECT id, login, password_hash, application_id, is_admin FROM users WHERE login = ?");
$stmt->bind_param("s", $login);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Проверка пароля
if ($user && password_verify($password, $user['password_hash'])) {
    // Устанавливаем сессионные переменные
    $_SESSION['user_id'] = $user['application_id'];
    $_SESSION['user_login'] = $user['login'];
    $_SESSION['logged_in'] = true;

    // Проверяем, является ли пользователь администратором
    if ($user['is_admin'] == 1) {
        // Если это админ, перенаправляем в админ-панель
        $_SESSION['is_admin'] = true;
        header('Location: admin_panel.php');
    } else {
        // Если это обычный пользователь, перенаправляем на форму
        $_SESSION['is_admin'] = false;
        header('Location: form.php');
    }
    exit;
} else {
    // Если ошибка в логине или пароле
    $_SESSION['login_error'] = "Неверный пароль или логин";
    header('Location: index.php');
    exit;
}
?>
