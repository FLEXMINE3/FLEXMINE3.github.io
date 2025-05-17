<?php
    require_once 'config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="main.css">
    <link rel="icon" href="unnamed.jpg">
    <title>Войти</title>
</head>
<style>
body {  
            background-image:url("four.jpg");
            background-size:cover;
        }
        
    </style>
<body>
    <div class="login-container">
        <h2>Вход в сессию</h2>
        
        <?php if(!empty($_SESSION['login_error'])):?>
            <div class = "login_error_container">
                <?= htmlspecialchars($_SESSION['login_error'])?>
            </div>

            <?php unset($_SESSION['login_error']); // Удаляем ошибку после показа ?>
        <?php endif; ?>

        <form action="auth.php" method = "POST">
            <label for="login">Логин</label>
            <input type="text" name="login" id="login" required>

            <label for="password">Пароль</label>
            <input type="password" name="password" id="password" required>

            <button type="submit">Войти</button>

            <div class = "register_container">
                Нет аккаунта?
                <a href="form.php">Зарегистрируйтесь сейчас</a>
            </div>
        </form>
    </div>
</body>
</html>