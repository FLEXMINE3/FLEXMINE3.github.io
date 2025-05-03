<?php

require_once 'config.php';
require_once 'funcs.php';

//Получение данных из формы
$name = $_POST['name'] ?? '';
$phone = $_POST['phone'] ?? '';
$email = $_POST['mail'] ?? '';
$birth = $_POST['bdate'] ?? '';
$gender = $_POST['gender'] ?? '';
$languages = $_POST['languages'] ?? [];
$bio = $_POST['bio'] ?? '';

// ВАЛИДАЦИЯ ДАННЫХ
$errors = [];

//Проверка ФИО
nameVal($patterns, $errors, $error_messages, $name);

// Проверка телефона
phoneVal($patterns, $errors, $error_messages, $phone);

// Проверка email
emailVal($patterns, $errors, $error_messages, $email);

// Проверка даты рождения
birthVal($errors, $error_messages, $birth);

// Проверка пола
genderVal($errors, $error_messages, $gender);

// Проверка языков программирования
langVal($errors, $error_messages, $languages);

// Проверка биографии
bioVal($patterns, $errors, $error_messages, $bio);

//Проверка чекбокса
conVal($errors);

// Если есть ошибки - сохраняем в сессию и cookies, перенаправляем обратно
if (!empty($errors)) {
    $_SESSION['errors'] = $errors;

    // Сохраняем введенные данные в cookies до конца сессии
    $data_to_save = [
        'name' => $name,
        'phone' => $phone,
        'mail' => $email,
        'bdate' => $birth,
        'gender' => $gender,
        'languages' => $languages,
        'bio' => $bio
    ];

    foreach ($data_to_save as $key => $value) {
        if (is_array($value)) {
            setcookie($key, json_encode($value), 0, '/');
        } else {
            setcookie($key, htmlspecialchars($value), 0, '/');
        }
    }
    
    header('Location: form.php');
    exit;
}

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']){
    try{
        $stmt = $conn->prepare("UPDATE application SET 
        name = ?, phone = ?, email = ?, dob = ?, gender = ?, bio = ?
        WHERE id = ?;
    ");
        $stmt->bind_param("ssssssi", $name, $phone, $email, $birth, $gender, $bio, $_SESSION['user_id']);
        $stmt->execute();
        $stmt->close();

        $stmt = $conn->prepare("DELETE FROM application_languages WHERE application_id = ?");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $stmt->close();

        $languageIds = [];
        if (!empty($languages)) {
            //'???'
            $placeholders = implode(',', array_fill(0, count($languages), '?'));
            //'sss'
            $types = str_repeat('s', count($languages));
    
            $stmt = $conn->prepare("SELECT id FROM languages WHERE name IN ($placeholders)");
            $stmt->bind_param($types, ...$languages);
            $stmt->execute();
            $result = $stmt->get_result();
            
            while ($row = $result->fetch_assoc()) {
                $languageIds[] = $row['id'];
            }
            $stmt->close();
        }

        if (!empty($languageIds)) {
            $stmt = $conn->prepare("INSERT INTO application_languages (application_id, language_id) VALUES (?, ?)");
            foreach ($languageIds as $langId) {
                $stmt->bind_param("ii", $_SESSION['user_id'], $langId);
                $stmt->execute();
            }
            $stmt->close();
        }

        $_SESSION['update_success'] = true;
        header('Location: form.php');
        exit;
        
    }catch (Exception $e) {
        // Логирование ошибки
        error_log("Database error: " . $e->getMessage());
        // Сообщение пользователю
        $_SESSION['errors']['db'] = "Произошла ошибка при сохранении данных. Пожалуйста, попробуйте позже.";
        header('Location: form.php');
        exit;
    }
}
else{
    try {
        $stmt = $conn->prepare("INSERT INTO application (name, phone, email, dob, gender, bio)
                                    VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $name, $phone, $email, $birth, $gender, $bio);
        $stmt->execute();
        $personId = $stmt->insert_id;
        $stmt->close();
    
        // Получаем ID выбранных языков программирования
        $languageIds = [];
        if (!empty($languages)) {
            //'???'
            $placeholders = implode(',', array_fill(0, count($languages), '?'));
            //'sss'
            $types = str_repeat('s', count($languages));
    
            $stmt = $conn->prepare("SELECT id FROM languages WHERE name IN ($placeholders)");
            $stmt->bind_param($types, ...$languages);
            $stmt->execute();
            $result = $stmt->get_result();
            
            while ($row = $result->fetch_assoc()) {
                $languageIds[] = $row['id'];
            }
            $stmt->close();
        }
    
         // Сохраняем связь пользователя с языками
         if (!empty($languageIds)) {
            $stmt = $conn->prepare("INSERT INTO application_languages (application_id, language_id) VALUES (?, ?)");
            foreach ($languageIds as $langId) {
                $stmt->bind_param("ii", $personId, $langId);
                $stmt->execute();
            }
            $stmt->close();
        }
    
        // Сохраняем данные в cookies на 1 год
        $cookie_time = time() + (86400 * 365);
        $data_to_save = [
            'name' => $name,
            'phone' => $phone,
            'mail' => $email,
            'bdate' => $birth,
            'gender' => $gender,
            'languages' => $languages,
            'bio' => $bio
        ];
        
        foreach ($data_to_save as $key => $value) {
            if (is_array($value)) {
                setcookie($key, json_encode($value), $cookie_time, '/');
            } else {
                setcookie($key, htmlspecialchars($value), $cookie_time, '/');
            }
        }
    
        //Создаем имя пользователя и пароль, отправляем в БД
        $login = 'user_' . $personId;
        $password = bin2hex(random_bytes(8)); //рандомный пароль из 16 символов
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users(application_id, login, password_hash) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $personId, $login, $hashed_password);
        $stmt->execute();
        $stmt->close();

        $_SESSION['generated_credentials'] = [
            'login' => $login,
            'password' => $password // Только для одноразового показа!
        ];
    
        // Очищаем ошибки и устанавливаем флаг успеха
        unset($_SESSION['errors']);
        $_SESSION['success'] = true;
        
        header('Location: form.php');
        exit;
    }catch (Exception $e) {
        // Логирование ошибки
        error_log("Database error: " . $e->getMessage());
        
        // Сообщение пользователю
        $_SESSION['errors']['db'] = "Произошла ошибка при сохранении данных. Пожалуйста, попробуйте позже.";
        header('Location: form.php');
        exit;
    } finally {
        $conn->close();
    }
}
?>