<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Форма заявки</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        form { max-width: 400px; margin: auto; }
        label, input, select, textarea { display: block; width: 100%; margin-bottom: 10px; }
    </style>
</head>
<body>
    <form action="index.php" method="POST">
        <label>ФИО: <input type="text" name="fio" required></label>
        <label>Телефон: <input type="tel" name="phone" required></label>
        <label>Email: <input type="email" name="email" required></label>
        <label>Дата рождения: <input type="date" name="dob" required></label>
        <label>Пол:
            <input type="radio" name="gender" value="male" required> Мужской
            <input type="radio" name="gender" value="female"> Женский
        </label>
        <label>Любимый язык программирования:
            <select name="languages[]" multiple required>
                <option value="1">Pascal</option>
                <option value="2">C</option>
                <option value="3">C++</option>
                <option value="4">JavaScript</option>
                <option value="5">PHP</option>
                <option value="6">Python</option>
                <option value="7">Java</option>
                <option value="8">Haskell</option>
                <option value="9">Clojure</option>
                <option value="10">Prolog</option>
                <option value="11">Scala</option>
                <option value="12">Go</option>
            </select>
        </label>
        <label>Биография: <textarea name="bio" required></textarea></label>
        <label><input type="checkbox" name="contract" required> С контрактом ознакомлен(а)</label>
        <input type="submit" value="Сохранить">
    </form>
</body>
</html>
