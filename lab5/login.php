<?php
session_start();

$pdo = new PDO(
    'mysql:host=localhost;dbname=u82380',
    'u82380',
    '43t3w4wE$'
);

$login = $_POST['login'] ?? '';
$password = $_POST['password'] ?? '';

$stmt = $pdo->prepare("SELECT * FROM application WHERE login=?");
$stmt->execute([$login]);

$user = $stmt->fetch();

if ($user && password_verify($password, $user['password_hash'])) {

    $_SESSION['user_id'] = $user['id'];

    header("Location: index.php");
    exit();
}

echo "Неверный логин или пароль";