<?php
    require_once 'config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="main.css">
    <title>Log In</title>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        
        <?php if(!empty($_SESSION['login_error'])):?>
            <div class = "login_error_container">
                <?= htmlspecialchars($_SESSION['login_error'])?>
            </div>

            <?php unset($_SESSION['login_error']); // Удаляем ошибку после показа ?>
        <?php endif; ?>

        <form action="auth.php" method = "POST">
            <label for="login">Login</label>
            <input type="text" name="login" id="login" required>

            <label for="password">Password</label>
            <input type="password" name="password" id="password" required>

            <button type="submit">Log In</button>

            <div class = "register_container">
                No account?
                <a href="form.php">Register now</a>
            </div>
        </form>
    </div>
</body>
</html>