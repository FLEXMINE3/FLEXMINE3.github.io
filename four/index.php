<?php
session_start();
header('Content-Type: text/html; charset=UTF-8');

// Подключение к БД
$user = 'u68807';
$pass = '3432023';
$dbname = 'u68807';
$host = 'localhost';

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("Ошибка подключения к БД: " . $e->getMessage());
}

// Инициализация переменных
$errors = [];
$data = [];

// Обработка GET-запроса (отображение формы)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!empty($_COOKIE['errors'])) {
        $errors = json_decode($_COOKIE['errors'], true);
        setcookie('errors', '', time() - 3600, '/'); // Очистка ошибок после отображения
    }
    if (!empty($_COOKIE['form_data'])) {
        $data = json_decode($_COOKIE['form_data'], true);
    }
    include 'form.php';
    exit();
}

// Функция валидации
function validate($field, $pattern, $error_message) {
    return preg_match($pattern, $field) ? null : $error_message;
}

// Обработка POST-запроса (отправка формы)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получение данных
    $data = [
        'fio' => trim($_POST['fio']),
        'phone' => trim($_POST['phone']),
        'email' => trim($_POST['email']),
        'dob' => trim($_POST['dob']),
        'gender' => $_POST['gender'] ?? '',
        'languages' => $_POST['languages'] ?? [],
        'bio' => trim($_POST['bio']),
        'contract' => isset($_POST['contract']) ? 'yes' : ''
    ];

    // Валидация полей
    $errors['fio'] = validate($data['fio'], "/^[\p{L} \-]+$/u", "ФИО может содержать только буквы, пробел и дефис.");
    $errors['phone'] = validate($data['phone'], "/^\+?[0-9\-\s]+$/", "Телефон должен содержать только цифры, пробелы, дефис и '+'.");
    $errors['email'] = filter_var($data['email'], FILTER_VALIDATE_EMAIL) ? null : "Некорректный формат e-mail.";
    $errors['dob'] = empty($data['dob']) ? "Дата рождения обязательна." : null;
    $errors['gender'] = empty($data['gender']) ? "Выберите пол." : null;
    $errors['languages'] = empty($data['languages']) ? "Выберите хотя бы один язык." : null;
    $errors['bio'] = validate($data['bio'], "/^[\p{L}0-9.,!\-\s]+$/u", "Биография содержит недопустимые символы.");
    $errors['contract'] = empty($data['contract']) ? "Необходимо согласие с контрактом." : null;

    $errors = array_filter($errors);

    if (!empty($errors)) {
        // Сохраняем ошибки и введенные данные в Cookies
        setcookie('errors', json_encode($errors), 0, '/');
        setcookie('form_data', json_encode($data), 0, '/');
        header("Location: index.php");
        exit();
    }

    // Сохранение данных в БД
    try {
        $stmt = $db->prepare("INSERT INTO application (fio, phone, email, dob, gender, bio) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$data['fio'], $data['phone'], $data['email'], $data['dob'], $data['gender'], $data['bio']]);
        $app_id = $db->lastInsertId();

        $stmt = $db->prepare("INSERT INTO application_languages (app_id, lang_id) VALUES (?, ?)");
        foreach ($data['languages'] as $lang) {
            $stmt->execute([$app_id, $lang]);
        }

        // Сохранение введенных данных на год
        foreach ($data as $key => $value) {
            setcookie($key, is_array($value) ? json_encode($value) : $value, time() + 31536000, '/');
        }

        // Очистка ошибок
        setcookie('errors', '', time() - 3600, '/');
        setcookie('form_data', '', time() - 3600, '/');
        header("Location: index.php?save=1");
        exit();
    } catch (PDOException $e) {
        die("Ошибка сохранения: " . $e->getMessage());
    }
}
?>
