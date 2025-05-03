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
    $errors = [];

    if (!preg_match('/^[a-zA-Zа-яА-Я\s]{1,150}$/u', $_POST['fio'])) {
        $errors['fio'] = 'ФИО: только буквы и пробелы, до 150 символов';
    }

    if (!preg_match('/^\+?[1-9]\d{1,14}$/', $_POST['phone'])) {
        $errors['phone'] = 'Телефон: только цифры и +, пример: +79991234567';
    }

    if (!preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $_POST['email'])) {
        $errors['email'] = 'Email: неверный формат, пример: user@example.com';
    }

    $birthdate = new DateTime($_POST['birthdate']);
    $now = new DateTime();
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $_POST['birthdate']) || $birthdate >= $now) {
        $errors['birthdate'] = 'Дата рождения: должна быть в прошлом в формате ГГГГ-ММ-ДД';
    }

    if (!preg_match('/^(male|female)$/', $_POST['gender'])) {
        $errors['gender'] = 'Пол: выберите мужской или женский';
    }

    $valid_languages = range(1, 12);
    if (empty($_POST['languages'])) {
        $errors['languages'] = 'Языки: выберите хотя бы один';
    } else {
        foreach ($_POST['languages'] as $lang) {
            if (!in_array($lang, $valid_languages)) {
                $errors['languages'] = 'Языки: неверное значение';
                break;
            }
        }
    }

    if (!preg_match('/^[\w\sа-яА-Я.,!?-]{1,1000}$/u', $_POST['bio'])) {
        $errors['bio'] = 'Биография: буквы, цифры, пробелы и знаки .,!?- до 1000 символов';
    }

    if (!isset($_POST['contract'])) {
        $errors['contract'] = 'Контракт: необходимо согласие';
    }

    if (!empty($errors)) {
        setcookie('form_errors', json_encode($errors), 0, '/');
        foreach ($_POST as $key => $value) {
            if ($key === 'languages') {
                setcookie($key, implode(',', $value), 0, '/');
            } else {
                setcookie($key, $value, 0, '/');
            }
        }
        header('Location: index.php');
        exit;
    } else {
        $stmt = $pdo->prepare('INSERT INTO applications (fio, phone, email, birthdate, gender, bio, contract) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            $_POST['fio'],
            $_POST['phone'],
            $_POST['email'],
            $_POST['birthdate'],
            $_POST['gender'],
            $_POST['bio'],
            1
        ]);

        $application_id = $pdo->lastInsertId();
        $stmt = $pdo->prepare('INSERT INTO application_languages (application_id, language_id) VALUES (?, ?)');
        foreach ($_POST['languages'] as $lang) {
            $stmt->execute([$application_id, $lang]);
        }

        $login = 'user_' . $application_id . '_' . rand(1000, 9999);
        $raw_password = bin2hex(random_bytes(4));
        $password_hash = password_hash($raw_password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare('INSERT INTO users (login, password_hash, application_id) VALUES (?, ?, ?)');
        $stmt->execute([$login, $password_hash, $application_id]);

        $expire = time() + 365 * 24 * 60 * 60;
        foreach ($_POST as $key => $value) {
            if ($key === 'languages') {
                setcookie($key, implode(',', $value), $expire, '/');
            } else {
                setcookie($key, $value, $expire, '/');
            }
        }
        setcookie('form_errors', '', time() - 3600, '/');
        $_SESSION['success_message'] = "Данные успешно сохранены. Логин: $login, Пароль: $raw_password. Сохраните эти данные!";
        header('Location: index.php');
        exit;
    }
}
ob_end_flush();
?>