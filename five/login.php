<?php
ob_start();
session_start();

$dsn = 'mysql:host=localhost;dbname=u68804';
$username = 'u68807';
$password = '3432023';
try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Ошибка подключения: ' . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['login'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare('SELECT * FROM users WHERE login = ?');
    $stmt->execute([$login]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['application_id'] = $user['application_id'];
        header('Location: edit.php');
        exit;
    } else {
        $error = 'Неверный логин или пароль';
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Вход</h1>
    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
    <form action="login.php" method="post">
        <label for="login">Логин:</label>
        <input type="text" id="login" name="login" required><br>
        <label for="password">Пароль:</label>
        <input type="password" id="password" name="password" required><br>
        <input type="submit" value="Войти">
    </form>
    <p><a href="index.php">Вернуться к форме</a></p>
</body>
</html>
<?php ob_end_flush(); ?>