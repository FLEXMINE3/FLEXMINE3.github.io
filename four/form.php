<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Форма регистрации</title>
    <style>
        .error { color: red; font-size: 0.9em; }
        .error-field { border: 1px solid red; }
    </style>
</head>
<body>
    <form action="index.php" method="post">
        <h2>Регистрация</h2>

        <label for="fio">ФИО:</label>
        <input type="text" id="fio" name="fio" value="<?= htmlspecialchars($data['fio'] ?? '') ?>" class="<?= isset($errors['fio']) ? 'error-field' : '' ?>">
        <div class="error"><?= $errors['fio'] ?? '' ?></div>

        <label for="phone">Телефон:</label>
        <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($data['phone'] ?? '') ?>" class="<?= isset($errors['phone']) ? 'error-field' : '' ?>">
        <div class="error"><?= $errors['phone'] ?? '' ?></div>

        <label for="email">E-mail:</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($data['email'] ?? '') ?>" class="<?= isset($errors['email']) ? 'error-field' : '' ?>">
        <div class="error"><?= $errors['email'] ?? '' ?></div>

        <label for="dob">Дата рождения:</label>
        <input type="date" id="dob" name="dob" value="<?= htmlspecialchars($data['dob'] ?? '') ?>" class="<?= isset($errors['dob']) ? 'error-field' : '' ?>">
        <div class="error"><?= $errors['dob'] ?? '' ?></div>

        <label>Пол:</label>
        <select name="gender">
            <option value="male" <?= (isset($data['gender']) && $data['gender'] === 'male') ? 'selected' : '' ?>>Мужской</option>
            <option value="female" <?= (isset($data['gender']) && $data['gender'] === 'female') ? 'selected' : '' ?>>Женский</option>
        </select>
        <div class="error"><?= $errors['gender'] ?? '' ?></div>

        <label for="bio">Биография:</label>
        <textarea id="bio" name="bio" class="<?= isset($errors['bio']) ? 'error-field' : '' ?>"><?= htmlspecialchars($data['bio'] ?? '') ?></textarea>
        <div class="error"><?= $errors['bio'] ?? '' ?></div>

        <label><input type="checkbox" name="contract" value="yes" <?= isset($data['contract']) ? 'checked' : '' ?>> Согласен с условиями</label>
        <div class="error"><?= $errors['contract'] ?? '' ?></div>

        <button type="submit">Сохранить</button>
    </form>
</body>
</html>
