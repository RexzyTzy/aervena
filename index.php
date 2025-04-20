<?php
session_start();

// === Konfigurasi Koneksi Database ===
$host = "152.42.231.255";
$user = "u30_8ueeOu60Zy";
$pass = "EqHhiT=oK5bJlF.QmMtfAEm=";
$dbname = "s30_kontol";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

// === Logout Handler ===
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

// === Login Handler ===
$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM accounts WHERE pName = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows == 1) {
        $user = $res->fetch_assoc();

        $md5_salt = md5($user['pass_salt']);
        $md5_pass = md5($password);
        $hashed = md5($md5_salt . $md5_pass);

        if ($hashed === $user['pPassword']) {
            $_SESSION['user'] = $user;
            header("Location: index.php");
            exit();
        } else {
            $error = "Password salah.";
        }
    } else {
        $error = "Username tidak ditemukan.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Kontrol Panel Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 500px;
            margin: 80px auto;
            background: white;
            padding: 30px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        h2, h3 {
            text-align: center;
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button, .btn {
            display: block;
            width: 100%;
            padding: 10px;
            background: #007bff;
            border: none;
            color: white;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover, .btn:hover {
            background: #0056b3;
        }
        .alert {
            color: white;
            background: #dc3545;
            padding: 10px;
            text-align: center;
            border-radius: 5px;
            margin-top: 10px;
        }
        .logout {
            text-align: center;
            margin-top: 20px;
        }
        .logout a {
            color: #fff;
            background: #dc3545;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
        }
        .logout a:hover {
            background: #a71d2a;
        }
    </style>
</head>
<body>

<div class="container">
    <?php if (!isset($_SESSION['user'])): ?>
        <h2>Login Panel</h2>
        <form method="post">
            <label>Username:</label>
            <input type="text" name="username" required>
            <label>Password:</label>
            <input type="password" name="password" required>
            <button type="submit" name="login">Login</button>
        </form>
        <?php if ($error): ?>
            <div class="alert"><?= $error ?></div>
        <?php endif; ?>
    <?php else: ?>
        <?php $user = $_SESSION['user']; ?>
        <h3>Selamat datang, <?= htmlspecialchars($user['pName']) ?>!</h3>
        <p><strong>ID Skin:</strong> <?= $user['pSkin'] ?></p>
        <p><strong>Duit Tangan:</strong> $<?= number_format($user['pCash']) ?></p>
        <p><strong>Duit ATM:</strong> $<?= number_format($user['pBank']) ?></p>
        <p><strong>Terakhir Login:</strong> <?= $user['pLogin'] ?></p>
        <div class="logout">
            <a href="?logout=true">Logout</a>
        </div>
    <?php endif; ?>
</div>

</body>
</html>