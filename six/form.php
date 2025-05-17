<?php

require_once 'config.php';

// Обработка выхода
if (isset($_GET['logout'])) {
    // Очищаем все данные сессии
    $_SESSION = [];
    session_destroy();
    
    // Удаляем куки сессии
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    header('Location: index.php');
    exit;
}

// Получаем сохраненные данные из cookies
$form_data = [
    'name' => $_COOKIE['name'] ?? '',
    'phone' => $_COOKIE['phone'] ?? '',
    'mail' => $_COOKIE['mail'] ?? '',
    'bdate' => $_COOKIE['bdate'] ?? '',
    'gender' => $_COOKIE['gender'] ?? '',
    'languages' => isset($_COOKIE['languages']) ? json_decode($_COOKIE['languages'], true) : [],
    'bio' => $_COOKIE['bio'] ?? ''
];

// Получаем ошибки из сессии
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']);

// Получаем флаг успешной отправки
$success = $_SESSION['success'] ?? false;
unset($_SESSION['success']);
$update_success = $_SESSION['update_success'] ?? false;
unset($_SESSION['update_success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="main.css">
    <link rel="icon" href="unnamed.jpg">
    <title>Задание 6</title>
    <style>
        .error { color: red; font-size: 0.8rem; margin-top: -0.5rem; margin-bottom: 0.5rem; }
        .error-field { border-color: red !important; }
        .success-message { color: green; margin-bottom: 1rem; text-align: center; }
    </style>
</head>
<style>
body {  
            background-image:url("four.jpg");
            background-size:cover;
        }
         
    </style>
<body>
    <div class="form_container">

    <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
    <div class="logout-container">
        <span>Добро пожаловать, <?= htmlspecialchars($_SESSION['user_login'] ?? 'User') ?></span>
        <a href="?logout=1" class="logout-btn">Выход из системы</a>
    </div>
<?php endif; ?>

    <?php if ($success): ?>
    <div class="success-message">
        <p>Данные успешно сохранены!</p>
        <?php if (isset($_SESSION['generated_credentials'])): ?>
            <p>
                Ваш логин: <strong><?= htmlspecialchars($_SESSION['generated_credentials']['login']) ?></strong>
            </p>
            <p>
                Ваш пароль: <strong><?= htmlspecialchars($_SESSION['generated_credentials']['password']) ?></strong>
            </p>
            <p class="warning-note">
                (Пожалуйста, сохраните эту информацию, пароль не может быть восстановлен)
            </p>
            <p>You can log in</p><a href="index.php">здесь</a>
            <?php unset($_SESSION['generated_credentials']); ?>
        <?php endif; ?>
    </div>
<?php endif; ?>

        <?php if($update_success): ?>
            <div class = "update_success_message">
                Ваши данные были обновлены
            </div>
        <?php endif;?>

        <?php if (!empty($errors)): ?>
            <div class="error-message">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li class="error"><?= htmlspecialchars(is_array($error) ? implode('<br>', $error) : $error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="database.php" method="post">
            <label for="name">Ваше полное имя*</label>
            <input type="text" name="name" id="name" value="<?= htmlspecialchars($form_data['name']) ?>" 
                   class="<?= isset($errors['name']) ? 'error-field' : '' ?>"
                    <?= $success ? 'disabled' : '' ?>>

            <?php if (isset($errors['name'])): ?>
                <div class="error"><?= htmlspecialchars($errors['name']) ?></div>
            <?php endif; ?>
            
            <label for="phone">Номер телефона*</label>
            <input type="tel" name="phone" id="phone" value="<?= htmlspecialchars($form_data['phone']) ?>" 
                   class="<?= isset($errors['phone']) ? 'error-field' : '' ?>"
                   <?= $success ? 'disabled' : '' ?>>
            <?php if (isset($errors['phone'])): ?>
                <div class="error"><?= htmlspecialchars($errors['phone']) ?></div>
            <?php endif; ?>
    
            <label for="mail">Электронная почта*</label>
            <input type="email" name="mail" id="mail" value="<?= htmlspecialchars($form_data['mail']) ?>" 
                   class="<?= isset($errors['mail']) ? 'error-field' : '' ?>"
                   <?= $success ? 'disabled' : '' ?>>
            <?php if (isset($errors['mail'])): ?>
                <div class="error"><?= htmlspecialchars($errors['mail']) ?></div>
            <?php endif; ?>
    
            <label for="bdate">Дата рождения*</label>
            <input type="date" name="bdate" id="bdate" value="<?= htmlspecialchars($form_data['bdate']) ?>" 
                    class="<?= isset($errors['birth']) ? 'error-field' : '' ?>"
                    <?= $success ? 'disabled' : '' ?>>
            <?php if (isset($errors['bdate'])): ?>
                <div class="error"><?= htmlspecialchars($errors['birth']) ?></div>
            <?php endif; ?>
               
            <label for="gender_choice">Пол*</label>
            <div class="radio_container" id="gender_choice">
                <input type="radio" id="male" name="gender" value="male" 
                       <?= $form_data['gender'] === 'male' ? 'checked' : '' ?>
                       <?= $success ? 'disabled' : '' ?>>
                <label for="male">Мужской</label>

                <input type="radio" id="female" name="gender" value="female" 
                       <?= $form_data['gender'] === 'female' ? 'checked' : '' ?>
                       <?= $success ? 'disabled' : '' ?>>
                <label for="female">Женский</label>
            </div>
            <?php if (isset($errors['gender'])): ?>
                <div class="error"><?= htmlspecialchars($errors['gender']) ?></div>
            <?php endif; ?>
    
            <label for="languages">Языки программирования* (Выберите несколько вариантов)</label>
            <select name="languages[]" id="languages" multiple class="<?= isset($errors['languages']) ? 'error-field' : '' ?>"
            <?= $success ? 'disabled' : '' ?>>
                <?php
                $all_languages = ['C++', 'Pascal', 'C', 'JavaScript', 'PHP', 'Python', 'Java', 'Haskell', 'Clojure', 'Prolog', 'Scala', 'Go'];
                foreach ($all_languages as $lang): ?>
                    <option value="<?= htmlspecialchars($lang) ?>" 
                            <?= in_array($lang, $form_data['languages']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($lang) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($errors['languages'])): ?>
                <div class="error"><?= htmlspecialchars($errors['languages']) ?></div>
            <?php endif; ?>

            <label for="bio">Напишите о себе</label>
            <textarea name="bio" id="bio" rows="5" cols="30"
    class="<?= isset($errors['bio']) ? 'error-field' : '' ?>"
    <?= $success ? 'disabled' : '' ?>><?= htmlspecialchars(trim($form_data['bio'])) ?></textarea>

            <?php if (isset($errors['bio'])): ?>
                <div class="error"><?= htmlspecialchars($errors['bio']) ?></div>
            <?php endif; ?>

            <div class="chek_container">
                <input type="checkbox" name="contract" id="contract"
                <?= $success ? 'checked disabled' : '' ?>>
                <label for="contract">Я прочитал и согласен*</label>
            </div>
            <?php if (isset($errors['contract'])): ?>
                <div class="error"><?= htmlspecialchars($errors['contract']) ?></div>
            <?php endif; ?>
            
            <?php if(isset($_SESSION['logged_in']) && $_SESSION['logged_in']):?>
                <input type="submit" value="Обновить">
            <?php elseif(!$success):?>
                <input type="submit" value="Отправить">
            <?php endif;?>
            
        </form>
    </div>
</body>

</html>