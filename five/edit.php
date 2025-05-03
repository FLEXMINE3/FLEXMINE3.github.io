<?php
ob_start();
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$dsn = 'mysql:host=localhost;dbname=u68804';
$username = 'u68807';
$password = '3432023';
try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Ошибка подключения: ' . $e->getMessage());
}

$stmt = $pdo->prepare('SELECT * FROM applications WHERE id = ?');
$stmt->execute([$_SESSION['application_id']]);
$app = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare('SELECT language_id FROM application_languages WHERE application_id = ?');
$stmt->execute([$_SESSION['application_id']]);
$current_languages = $stmt->fetchAll(PDO::FETCH_COLUMN);

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

    if (empty($errors)) {
        $stmt = $pdo->prepare('UPDATE applications SET fio = ?, phone = ?, email = ?, birthdate = ?, gender = ?, bio = ?, contract = ? WHERE id = ?');
        $stmt->execute([
            $_POST['fio'],
            $_POST['phone'],
            $_POST['email'],
            $_POST['birthdate'],
            $_POST['gender'],
            $_POST['bio'],
            1,
            $_SESSION['application_id']
        ]);

        $stmt = $pdo->prepare('DELETE FROM application_languages WHERE application_id = ?');
        $stmt->execute([$_SESSION['application_id']]);

        $stmt = $pdo->prepare('INSERT INTO application_languages (application_id, language_id) VALUES (?, ?)');
        foreach ($_POST['languages'] as $lang) {
            $stmt->execute([$_SESSION['application_id'], $lang]);
        }

        $_SESSION['success_message'] = 'Данные успешно обновлены.';
        header('Location: edit.php');
        exit;
    } else {
        setcookie('form_errors', json_encode($errors), 0, '/');
        foreach ($_POST as $key => $value) {
            if ($key === 'languages') {
                setcookie($key, implode(',', $value), 0, '/');
            } else {
                setcookie($key, $value, 0, '/');
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактирование данных</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Редактирование данных</h1>
    <?php if (isset($_SESSION['success_message'])): ?>
        <p style="color: green;"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></p>
    <?php endif; ?>
    <?php if (isset($errors) && !empty($errors)): ?>
        <div class="errors">
            <?php foreach ($errors as $message): ?>
                <p><?php echo $message; ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <form action="edit.php" method="post">
        <label for="fio">ФИО:</label>
        <input type="text" id="fio" name="fio" value="<?php echo htmlspecialchars($app['fio']); ?>" required><br>

        <label for="phone">Телефон:</label>
        <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($app['phone']); ?>" required><br>

        <label for="email">E-mail:</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($app['email']); ?>" required><br>

        <label for="birthdate">Дата рождения:</label>
        <input type="date" id="birthdate" name="birthdate" value="<?php echo htmlspecialchars($app['birthdate']); ?>" required><br>

        <label>Пол:</label>
        <input type="radio" id="male" name="gender" value="male" <?php echo $app['gender'] === 'male' ? 'checked' : ''; ?> required>
        <label for="male">Мужской</label>
        <input type="radio" id="female" name="gender" value="female" <?php echo $app['gender'] === 'female' ? 'checked' : ''; ?>>
        <label for="female">Женский</label><br>

        <label for="languages">Любимый язык программирования:</label>
        <select id="languages" name="languages[]" multiple required>
            <?php
            $languages = ['Pascal', 'C', 'C++', 'JavaScript', 'PHP', 'Python', 'Java', 'Haskel', 'Clojure', 'Prolog', 'Scala', 'Go'];
            foreach ($languages as $i => $lang):
            ?>
                <option value="<?php echo $i+1; ?>" <?php echo in_array($i+1, $current_languages) ? 'selected' : ''; ?>>
                    <?php echo $lang; ?>
                </option>
            <?php endforeach; ?>
        </select><br>

        <label for="bio">Биография:</label>
        <textarea id="bio" name="bio" rows="4" required><?php echo htmlspecialchars($app['bio']); ?></textarea><br>

        <input type="checkbox" id="contract" name="contract" checked required>
        <label for="contract">С контрактом ознакомлен(а)</label><br>

        <input type="submit" value="Сохранить изменения">
    </form>
    <p><a href="logout.php">Выйти</a></p>
</body>
</html>
<?php ob_end_flush(); ?>