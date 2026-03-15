<?php

$errors = [];
$values = [];

$fio = $_POST['fio'] ?? '';
$phone = $_POST['phone'] ?? '';
$email = $_POST['email'] ?? '';
$birthdate = $_POST['birthdate'] ?? '';
$gender = $_POST['gender'] ?? '';
$biography = $_POST['biography'] ?? '';
$languages = $_POST['languages'] ?? [];
$contract = isset($_POST['contract']);

$values = $_POST;

if (!preg_match("/^[a-zA-Zа-яА-Я\s]{1,150}$/u", $fio)) {
    $errors['fio'] = "Допустимы только буквы и пробелы (до 150 символов)";
}

if (!preg_match("/^\+?[0-9\s\-]{7,20}$/", $phone)) {
    $errors['phone'] = "Допустимы цифры, пробелы, + и -";
}

if (!preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $email)) {
    $errors['email'] = "Некорректный email";
}

if (!preg_match("/^[a-zA-Zа-яА-Я0-9\s.,!?-]{1,1000}$/u", $biography)) {
    $errors['biography'] = "Недопустимые символы";
}

if (empty($languages)) {
    $errors['languages'] = "Выберите хотя бы один язык";
}

if (!$contract) {
    $errors['contract'] = "Необходимо согласиться с контрактом";
}

if (!empty($errors)) {

    setcookie("errors", json_encode($errors), 0, "/");
    setcookie("values", json_encode($values), 0, "/");

    header("Location: index.php");
    exit();
}

try {

    $pdo = new PDO(
        'mysql:host=localhost;dbname=u82380',
        'u82380',
        '43t3w4wE$'
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("
        INSERT INTO application
        (fio, phone, email, birthdate, gender, biography, contract)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $fio,
        $phone,
        $email,
        $birthdate,
        $gender,
        $biography,
        $contract ? 1 : 0
    ]);

    $app_id = $pdo->lastInsertId();

    $stmtLang = $pdo->prepare("
        INSERT INTO application_language
        (application_id, language_id)
        VALUES (?, ?)
    ");

    foreach ($languages as $lang) {
        $stmtLang->execute([$app_id, $lang]);
    }

    setcookie("fio", "", time() - 3600, "/");
    setcookie("phone", "", time() - 3600, "/");
    setcookie("email", "", time() - 3600, "/");
    setcookie("values", "", time() - 3600, "/");

    setcookie("success", "1", 0, "/");

    header("Location: index.php");
    exit();

}
catch (PDOException $e) {
    echo "Ошибка базы данных: " . $e->getMessage();
}