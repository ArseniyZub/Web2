<?php
session_start();

$errors = [];
$values = [];
$success = false;

if (isset($_COOKIE["errors"])) {
    $errors = json_decode($_COOKIE["errors"], true);
    setcookie("errors", "", time() - 3600);
}

if (isset($_COOKIE["values"])) {
    $values = json_decode($_COOKIE["values"], true);
    setcookie("values", "", time() - 3600);
}

if (isset($_COOKIE["success"])) {
    $success = true;
    setcookie("success", "", time() - 3600);
}

/* =================================
   LAB7 ADDED
   загрузка данных пользователя
================================= */

$user = null;
$languages_user = [];

if (isset($_SESSION['user_id'])) {

    $pdo = new PDO(
        'mysql:host=localhost;dbname=u82380',
        'u82380',
        '43t3w4wE$'
    );

    $stmt = $pdo->prepare("SELECT * FROM application WHERE id=?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    $stmt = $pdo->prepare("SELECT language_id FROM application_language WHERE application_id=?");
    $stmt->execute([$_SESSION['user_id']]);

    $languages_user = array_column($stmt->fetchAll(), 'language_id');
}

/* ================================= */
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Форма</title>
    </head>

    <body>

        <!--логин-->
        <?php if (!isset($_SESSION['user_id'])): ?>

        <form method="POST" action="login.php">
        Логин:<br>
        <input type="text" name="login"><br>
        Пароль:<br>
        <input type="password" name="password"><br>
        <button>Войти</button>
        </form>

        <hr>

        <?php else: ?>
        <a href="logout.php">Выйти</a>
        <?php endif; ?>

        <!--показ логина-->
        <?php if (isset($_SESSION['login'])): ?>
        <div>
        Логин: <?= $_SESSION['login'] ?><br>
        Пароль: <?= $_SESSION['password'] ?>
        </div>
        <?php unset($_SESSION['login'], $_SESSION['password']); endif; ?>

        <h2>Форма</h2>

        <form method="POST" action="submit.php">

        <input name="fio" value="<?= $user['fio'] ?? ($values['fio'] ?? '') ?>"><br>
        <input name="phone" value="<?= $user['phone'] ?? ($values['phone'] ?? '') ?>"><br>
        <input name="email" value="<?= $user['email'] ?? ($values['email'] ?? '') ?>"><br>
        <input type="date" name="birthdate"
        value="<?= $user['birthdate'] ?? ($values['birthdate'] ?? '') ?>"><br>

        <label><input type="radio" name="gender" value="1"
        <?= (($user['gender'] ?? '') == '1') ? 'checked' : '' ?>>М</label>

        <label><input type="radio" name="gender" value="2"
        <?= (($user['gender'] ?? '') == '2') ? 'checked' : '' ?>>Ж</label>

        <br>

        <select name="languages[]" multiple>
            <?php
            $all = [1=>"C",2=>"C++",3=>"PHP"];

            foreach ($all as $id=>$name):
            $selected = in_array($id, $languages_user) ? "selected" : "";
            ?>

            <option value="<?= $id ?>" <?= $selected ?>><?= $name ?></option>
            <?php endforeach; ?>

        </select>
        <textarea name="biography"><?= $user['biography'] ?? '' ?></textarea>
        <br>

        <input type="checkbox" name="contract"
        <?= isset($user['contract']) && $user['contract'] ? "checked" : "" ?>>

        <button>Сохранить</button>
        </form>

    </body>
</html>