<?php
    require_once 'config.php';

    $stmt = $conn->prepare("SELECT login, password_hash, application_id FROM users WHERE login = ?");
    $stmt->bind_param("s", $_POST['login']);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    
    if($user && password_verify($_POST['password'], $user['password_hash'])){
        //user_id хранит id формы пользователя
        $_SESSION['user_id'] = $user['application_id'];
        $_SESSION['user_login'] = $user['login']; // Добавляем логин в сессию
        $_SESSION['logged_in'] = true; // Флаг авторизации
        header('Location: form.php');
        exit;
    }
    else{
        $_SESSION['login_error'] = "Incorrect password or login";
        header('Location: index.php');
        exit;
    }
?>

