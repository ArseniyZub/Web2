<!DOCTYPE html>
<html>
    <head>
    <meta charset="UTF-8">
    <title>Админ панель</title>

    <style>
        table { border-collapse: collapse; width: 100%; }
        td, th { border: 1px solid #ccc; padding: 8px; }
    </style>

    </head>

    <body>
        <h1>Админ панель</h1>

        <h2>Все заявки</h2>

        <table>
        <tr>
            <th>ID</th>
            <th>ФИО</th>
            <th>Email</th>
            <th>Действия</th>
        </tr>

        <?php foreach ($apps as $app): ?>

        <tr>
            <td><?= $app['id'] ?></td>
            <td><?= htmlspecialchars($app['fio']) ?></td>
            <td><?= htmlspecialchars($app['email']) ?></td>

            <td>
                <a href="?delete=<?= $app['id'] ?>">Удалить</a>
            </td>
        </tr>

        <?php endforeach; ?>
        </table>

        <h2>Статистика языков</h2>

        <ul>
        <?php foreach ($languages as $id => $name): ?>
            <li>
                <?= $name ?>:
                <?= $stats[$id] ?? 0 ?>
            </li>
        <?php endforeach; ?>
        </ul>

    </body>
</html>



<?php
if (empty($_SERVER['PHP_AUTH_USER']) ||
    empty($_SERVER['PHP_AUTH_PW']) ||
    $_SERVER['PHP_AUTH_USER'] != 'admin' ||
    $_SERVER['PHP_AUTH_PW'] != 'admin') {

    header('HTTP/1.1 401 Unauthorized');
    header('WWW-Authenticate: Basic realm="Admin panel"');

    echo "Нужна авторизация";
    exit();
}


$pdo = new PDO(
    'mysql:host=localhost;dbname=u82380',
    'u82380',
    '43t3w4wE$'
);

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


if (isset($_GET['delete'])) {

    $id = $_GET['delete'];

    $pdo->prepare("DELETE FROM application_language WHERE application_id=?")->execute([$id]);
    $pdo->prepare("DELETE FROM application WHERE id=?")->execute([$id]);

    header("Location: admin.php");
    exit();
}


$stmt = $pdo->query("SELECT * FROM application");
$apps = $stmt->fetchAll();



$stmt = $pdo->query("
    SELECT l.language_id, COUNT(*) as cnt
    FROM application_language l
    GROUP BY l.language_id
");

$stats = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

$languages = [
    1=>"Pascal",2=>"C",3=>"C++",4=>"JS",
    5=>"PHP",6=>"Python"
];

?>

