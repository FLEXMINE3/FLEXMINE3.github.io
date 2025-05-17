<?php
require_once 'config.php';

if (!isset($_SESSION['logged_in']) || empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}


$id = $_GET['id'] ?? null;
if (!$id) die("ID не передан");

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['login'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;
    $application_id = (int)$_POST['application_id'];

    $stmt = $conn->prepare("UPDATE users SET login=?, name=?, email=?, is_admin=?, application_id=? WHERE id=?");
    $stmt->bind_param("sssiii", $login, $name, $email, $is_admin, $application_id, $id);
    $stmt->execute();
    $stmt->close();

    header('Location: admin_panel.php');
    exit;
}

// Получение данных пользователя
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) die("Пользователь не найден");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Редактировать пользователя</title>
</head>
<body>
<h2 >Редактировать пользователя</h2>
<style>
    form{
        background-size:cover;
    }

body {  
            background-image:url("five.jpg");
            background-size:cover;
        }
        

</style>
<form  method="post">
    Login: <input type="text" name="login" value="<?= htmlspecialchars($user['login']) ?>" required><br>
    Name: <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>"><br>
    Email: <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>"><br>
    App ID: <input type="int" name="application_id" value="<?= $user['application_id'] ?>" required><br>
    Is Admin: <input type="checkbox" name="is_admin" <?= $user['is_admin'] ? 'checked' : '' ?>><br>
    <input type="submit" value="Обновить">
</form>
</body>
</html>
