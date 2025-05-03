<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$db_server = "localhost";
$db_name = 'u68807';
$db_user = 'u68807';
$db_pass = '3432023';

// Подключение к БД
$conn = new mysqli($db_server, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8");
