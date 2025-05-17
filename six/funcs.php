<?php

$patterns= [
    'name' => '/^[а-яА-ЯёЁa-zA-Z\-]+\s[а-яА-ЯёЁa-zA-Z\-]+\s[а-яА-ЯёЁa-zA-Z\-]+$/u',
    'phone' => '/^(\+7|8)[\s\-]?\(?\d{3}\)?[\s\-]?\d{3}[\s\-]?\d{2}[\s\-]?\d{2}$/',
    'email' => '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
    'bio' => '/^[\w\s\.,!?а-яА-ЯёЁ\-]{0,500}$/u'
];

$error_messages = [
    'name' => 'ФИО должно состоять ровно из 3 слов (только буквы, дефисы и пробелы)',
    'phone' => 'Номер телефона должен быть в формате +7 XXX XXX-XX-XX или 8 XXX XXX-XX-XX',
    'email' => 'Введите корректный email (например: user@example.com)',
    'birth' => 'Укажите дату рождения',
    'gender' => 'Укажите пол',
    'languages' => 'Выберите хотя бы 1 язык программирования',
    'bio' => 'Биография не должна содержать специальных символов (макс. 500 символов)'
];

function nameVal(&$patterns, &$errors, &$error_messages, $name){
    if(empty($name)){
        $errors['name'] = "ФИО обязательно для заполнения";
    }
    elseif(!preg_match($patterns['name'], $name)){
        $errors['name'] = $error_messages['name'];
    }
}

function phoneVal(&$patterns, &$errors, &$error_messages, $phone){
    if (empty($phone)) {
        $errors['phone'] = "Телефон обязателен для заполнения";
    } elseif (!preg_match($patterns['phone'], $phone)) {
        $errors['phone'] = $error_messages['phone'];
    }
}

function emailVal(&$patterns, &$errors, &$error_messages, $email){
    if (empty($email)) {
        $errors['mail'] = "Email обязателен для заполнения";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match($patterns['email'], $email)) {
        $errors['mail'] = $error_messages['email'];
    }
}

function birthVal(&$errors, &$error_messages, $birth){
    if (empty($birth)) {
        $errors['birth'] = $error_messages['birth'];
    }   
}

function genderVal(&$errors, &$error_messages, $gender){
    if (empty($gender)) {
        $errors['gender'] = $error_messages['gender'];
    }
}

function langVal(&$errors, &$error_messages, $languages){
    if (empty($languages)) {
        $errors['languages'] = $error_messages['languages'];
    }
}

function bioVal(&$patterns, &$errors, &$error_messages, $bio){
    if (!empty($bio) && !preg_match($patterns['bio'], $bio)) {
        $errors['bio'] = $error_messages['bio'];
    }
}

function conVal(&$errors){
    if (!isset($_POST['contract'])) {
        $errors['contract'] = "Вы должны согласиться с условиями";
    }
}

?>