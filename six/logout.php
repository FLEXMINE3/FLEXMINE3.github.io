<?php
session_start();

// Удалить все переменные сессии
$_SESSION = [];

// Уничтожить сессию
session_destroy();

// Перенаправить на страницу входа
header("Location: index.php");
exit;