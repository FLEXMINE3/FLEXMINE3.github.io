<?php
header('Content-Type: text/html; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (!empty($_GET['save'])) {
        echo 'Спасибо, результаты сохранены.';
    }
    include('form.php');
    exit();
}

$errors = false;
if (empty($_POST['fio']) || !preg_match('/^[a-zA-Zа-яА-ЯёЁ\s]{1,150}$/u', $_POST['fio'])) {
    echo 'Некорректное ФИО.<br/>';
    $errors = true;
}
if (empty($_POST['phone']) || !preg_match('/^\+?[0-9\-\s]{7,15}$/', $_POST['phone'])) {
    echo 'Некорректный телефон.<br/>';
    $errors = true;
}
if (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    echo 'Некорректный e-mail.<br/>';
    $errors = true;
}
if (empty($_POST['dob'])) {
    echo 'Введите дату рождения.<br/>';
    $errors = true;
}
if (empty($_POST['gender']) || !in_array($_POST['gender'], ['male', 'female'])) {
    echo 'Некорректный выбор пола.<br/>';
    $errors = true;
}
if (empty($_POST['languages']) || !is_array($_POST['languages'])) {
    echo 'Выберите хотя бы один язык программирования.<br/>';
    $errors = true;
}
if (empty($_POST['bio'])) {
    echo 'Введите биографию.<br/>';
    $errors = true;
}
if (empty($_POST['contract'])) {
    echo 'Вы должны согласиться с условиями.<br/>';
    $errors = true;
}

if ($errors) {
    exit();
}

$user = 'u68807';
$pass = '3432023';
$dbname = 'u68807';

try {
    $db = new PDO('mysql:host=localhost;dbname=' . $dbname, $user, $pass, [
        PDO::ATTR_PERSISTENT => true,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // Вставка заявки
    $stmt = $db->prepare("INSERT INTO application (fio, phone, email, dob, gender, bio) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$_POST['fio'], $_POST['phone'], $_POST['email'], $_POST['dob'], $_POST['gender'], $_POST['bio']]);

    $app_id = $db->lastInsertId();

    // Вставка языков программирования
    $stmt = $db->prepare("INSERT INTO application_languages (app_id, lang_id) VALUES (?, ?)");
    foreach ($_POST['languages'] as $lang) {
        $stmt->execute([$app_id, $lang]);
    }

    header('Location: ?save=1');
} catch (PDOException $e) {
    echo 'Ошибка: ' . $e->getMessage();
    exit();
}
