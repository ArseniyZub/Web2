<?php
if (
    empty($_SERVER['PHP_AUTH_USER']) ||
    empty($_SERVER['PHP_AUTH_PW']) ||
    $_SERVER['PHP_AUTH_USER'] != 'admin' ||
    $_SERVER['PHP_AUTH_PW'] != '123'
) {
    header('HTTP/1.1 401 Unauthorized');
    header('WWW-Authenticate: Basic realm="Admin panel"');
    echo "Требуется авторизация";
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
$apps = $stmt->fetchAll(PDO::FETCH_ASSOC);


$stmt = $pdo->query("
    SELECT language_id, COUNT(*) as cnt
    FROM application_language
    GROUP BY language_id
");


$stats = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);


$languages = [
    1 => "Pascal",
    2 => "C",
    3 => "C++",
    4 => "JavaScript",
    5 => "PHP",
    6 => "Python"
];

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Админ панель</title>

    <style>
        body {
            font-family: Arial;
            background: linear-gradient(135deg, #4e73df, #1cc88a);
            margin: 0;
        }

        .container {
            max-width: 1000px;
            margin: 40px auto;
            background: white;
            padding: 25px;
            border-radius: 12px;
        }

        h1 {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th {
            background: #4e73df;
            color: white;
        }

        td,
        th {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: center;
        }

        tr:hover {
            background: #f2f2f2;
        }

        .btn-delete {
            background: red;
            color: white;
            padding: 5px 10px;
            border-radius: 6px;
            text-decoration: none;
        }

        .stats {
            margin-top: 30px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 10px;
        }

        .stat-item {
            margin: 5px 0;
        }
    </style>

</head>

<body>

    <div class="container">

        <h1>Админ панель</h1>

        <h2>Все заявки</h2>

        <table>

            <tr>
                <th>ID</th>
                <th>ФИО</th>
                <th>Email</th>
                <th>Действия</th>
            </tr>

            <?php foreach ($apps ?? [] as $app): ?>

                <tr>
                    <td><?= $app['id'] ?></td>
                    <td><?= htmlspecialchars($app['fio']) ?></td>
                    <td><?= htmlspecialchars($app['email']) ?></td>

                    <td>
                        <a class="btn-delete" href="?delete=<?= $app['id'] ?>">Удалить</a>
                    </td>

                </tr>

            <?php endforeach; ?>

        </table>

        <h2>Статистика языков</h2>

        <div class="stats">

            <?php foreach ($languages as $id => $name): ?>

                <div class="stat-item">
                    <?= $name ?> — <b><?= $stats[$id] ?? 0 ?></b>
                </div>

            <?php endforeach; ?>

        </div>

    </div>

</body>

</html>