<?php
ob_start();
session_start();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Форма заявки</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Заполните форму заявки</h1>
    <?php if (isset($_SESSION['user_id'])): ?>
        <p>Вы вошли в систему. <a href="edit.php">Редактировать данные</a> | <a href="logout.php">Выйти</a></p>
    <?php else: ?>
        <p><a href="login.php">Войти</a></p>
    <?php endif; ?>
    <?php if (isset($_SESSION['success_message'])): ?>
        <p style="color: green;"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></p>
    <?php endif; ?>
    <?php
    $errors = isset($_COOKIE['form_errors']) ? json_decode($_COOKIE['form_errors'], true) : [];
    $prev_values = $_COOKIE;
    ?>
    
    <?php if (!empty($errors)): ?>
        <div class="errors">
            <?php foreach ($errors as $field => $message): ?>
                <p><?php echo $message; ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form action="form.php" method="post">
        <label for="fio">ФИО:</label>
        <input type="text" id="fio" name="fio" value="<?php echo htmlspecialchars($prev_values['fio'] ?? ''); ?>" class="<?php echo isset($errors['fio']) ? 'error' : ''; ?>" required><br>

        <label for="phone">Телефон:</label>
        <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($prev_values['phone'] ?? ''); ?>" class="<?php echo isset($errors['phone']) ? 'error' : ''; ?>" required><br>

        <label for="email">E-mail:</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($prev_values['email'] ?? ''); ?>" class="<?php echo isset($errors['email']) ? 'error' : ''; ?>" required><br>

        <label for="birthdate">Дата рождения:</label>
        <input type="date" id="birthdate" name="birthdate" value="<?php echo htmlspecialchars($prev_values['birthdate'] ?? ''); ?>" class="<?php echo isset($errors['birthdate']) ? 'error' : ''; ?>" required><br>

        <label>Пол:</label>
        <input type="radio" id="male" name="gender" value="male" <?php echo ($prev_values['gender'] ?? '') === 'male' ? 'checked' : ''; ?> required>
        <label for="male">Мужской</label>
        <input type="radio" id="female" name="gender" value="female" <?php echo ($prev_values['gender'] ?? '') === 'female' ? 'checked' : ''; ?>>
        <label for="female">Женский</label><br>

        <label for="languages">Любимый язык программирования:</label>
        <select id="languages" name="languages[]" multiple required>
            <?php
            $languages = ['Pascal', 'C', 'C++', 'JavaScript', 'PHP', 'Python', 'Java', 'Haskel', 'Clojure', 'Prolog', 'Scala', 'Go'];
            $prev_langs = isset($prev_values['languages']) ? explode(',', $prev_values['languages']) : [];
            foreach ($languages as $i => $lang):
            ?>
                <option value="<?php echo $i+1; ?>" <?php echo in_array($i+1, $prev_langs) ? 'selected' : ''; ?>>
                    <?php echo $lang; ?>
                </option>
            <?php endforeach; ?>
        </select><br>

        <label for="bio">Биография:</label>
        <textarea id="bio" name="bio" rows="4" required class="<?php echo isset($errors['bio']) ? 'error' : ''; ?>"><?php echo htmlspecialchars($prev_values['bio'] ?? ''); ?></textarea><br>

        <input type="checkbox" id="contract" name="contract" <?php echo isset($prev_values['contract']) ? 'checked' : ''; ?> required>
        <label for="contract">С контрактом ознакомлен(а)</label><br>

        <input type="submit" value="Сохранить">
    </form>
</body>
</html>
<?php ob_end_flush(); ?>