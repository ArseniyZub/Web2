<?php
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
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Задание 3</title>
    <link rel="stylesheet" href="style.css">

    <style>
        .error {
            border: 2px solid red !important;
        }

        .error-text {
            color: red;
            font-size: 13px;
            margin-top: 5px;
        }

        .success {
            background: #d4edda;
            color: #155724;
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 8px;
        }
    </style>
</head>

<body>

<?php if (!empty($errors)): ?>
    <div class="error-text">
    Пожалуйста исправьте ошибки в форме
    </div>
<?php endif; ?>

<form action="submit.php" method="POST">

    <h2>Форма заявки</h2>

    <?php if ($success): ?>
        <div class="success">
            Заявка успешно отправлена ✅
        </div>
    <?php endif; ?>

    ФИО:<br>
    <input
        type="text"
        name="fio"
        value="<?= htmlspecialchars($values['fio'] ?? $_COOKIE['fio'] ?? '') ?>"
        class="<?= isset($errors['fio']) ? 'error' : '' ?>"
    >

    <?php if (isset($errors['fio'])): ?>
        <div class="error-text"><?= $errors['fio'] ?></div>
    <?php endif; ?>

    <br><br>

    Телефон:<br>
    <input
        type="tel"
        name="phone"
        value="<?= htmlspecialchars($values['phone'] ?? $_COOKIE['phone'] ?? '') ?>"
        class="<?= isset($errors['phone']) ? 'error' : '' ?>"
    >

    <?php if (isset($errors['phone'])): ?>
        <div class="error-text"><?= $errors['phone'] ?></div>
    <?php endif; ?>

    <br><br>

    Email:<br>
    <input
        type="email"
        name="email"
        value="<?= htmlspecialchars($values['email'] ?? $_COOKIE['email'] ?? '') ?>"
        class="<?= isset($errors['email']) ? 'error' : '' ?>"
    >

    <?php if (isset($errors['email'])): ?>
        <div class="error-text"><?= $errors['email'] ?></div>
    <?php endif; ?>

    <br><br>

    Дата рождения:<br>
    <input
        type="date"
        name="birthdate"
        value="<?= htmlspecialchars($values['birthdate'] ?? '') ?>"
    >

    <br><br>

    Пол:<br>

    <label>
        <input
            type="radio"
            name="gender"
            value="male"
            <?= (isset($values['gender']) && $values['gender'] == "male") ? "checked" : "" ?>
        >
        Мужской
    </label>

    <label>
        <input
            type="radio"
            name="gender"
            value="female"
            <?= (isset($values['gender']) && $values['gender'] == "female") ? "checked" : "" ?>
        >
        Женский
    </label>

    <br><br>

    Любимые языки:<br>

    <select
        name="languages[]"
        multiple
        class="<?= isset($errors['languages']) ? 'error' : '' ?>"
    >

        <?php
        $languages = [
            1 => "Pascal",
            2 => "C",
            3 => "C++",
            4 => "JavaScript",
            5 => "PHP",
            6 => "Python",
            7 => "Java",
            8 => "Haskell",
            9 => "Clojure",
            10 => "Prolog",
            11 => "Scala",
            12 => "Go"
        ];

        foreach ($languages as $id => $name):
            $selected = (
                isset($values['languages']) &&
                in_array($id, $values['languages'])
            ) ? "selected" : "";
        ?>
            <option value="<?= $id ?>" <?= $selected ?>>
                <?= $name ?>
            </option>
        <?php endforeach; ?>

    </select>

    <?php if (isset($errors['languages'])): ?>
        <div class="error-text"><?= $errors['languages'] ?></div>
    <?php endif; ?>

    <br><br>

    Биография:<br>

    <textarea
        name="biography"
        class="<?= isset($errors['biography']) ? 'error' : '' ?>"
    ><?= htmlspecialchars($values['biography'] ?? '') ?></textarea>

    <?php if (isset($errors['biography'])): ?>
        <div class="error-text"><?= $errors['biography'] ?></div>
    <?php endif; ?>

    <br><br>

    <label>
        <input
            type="checkbox"
            name="contract"
            <?= isset($values['contract']) ? "checked" : "" ?>
            class="<?= isset($errors['contract']) ? 'error' : '' ?>"
        >
        С контрактом ознакомлен
    </label>

    <?php if (isset($errors['contract'])): ?>
        <div class="error-text"><?= $errors['contract'] ?></div>
    <?php endif; ?>

    <br><br>

    <button type="submit">Сохранить</button>

</form>

</body>
</html>