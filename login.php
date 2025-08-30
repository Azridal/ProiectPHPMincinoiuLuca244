  <?php
require 'db.php';
session_start();

// Prevenirea accesului utilizatorilor autentificati
if (isset($_SESSION['user_id'])) {
    // Redirectionare utilizatori autentificati
    if ($_SESSION['role'] === 'admin') {
        header("Location: admin.php");
    } else {
        header("Location: index.php");
    }
    exit();
}

// Procesarea autentificarii
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Ia utilizatorul din baza de date
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Seteaza sesiunea utilizatorului
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];

        // Redirectionare în funcție de rol
        if ($_SESSION['role'] === 'admin') {
            header("Location: admin.php");
        } else {
            header("Location: index.php");
        }
        exit();
    } else {
        $error = "Nume utilizator sau parolă incorectă!";
    }
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autentificare</title>
    <style>
        body {
            background: #00c6ff;
            background: -webkit-linear-gradient(to right, #0072ff, #00c6ff);
            background: linear-gradient(to right, #0072ff, #00c6ff);
            font-family: "Roboto", sans-serif;
        }

        .login-page {
            width: 360px;
            padding: 8% 0 0;
            margin: auto;
        }

        .form {
            position: relative;
            z-index: 1;
            background: #ffffff;
            max-width: 360px;
            margin: 0 auto 100px;
            padding: 45px;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .form input {
            font-family: "Roboto", sans-serif;
            outline: 0;
            background: #f2f2f2;
            width: 100%;
            border: 0;
            margin: 0 0 15px;
            padding: 15px;
            box-sizing: border-box;
            font-size: 14px;
        }

        .form button {
            font-family: "Roboto", sans-serif;
            text-transform: uppercase;
            outline: 0;
            background: #0072ff;
            width: 100%;
            border: 0;
            padding: 15px;
            color: #ffffff;
            font-size: 14px;
            cursor: pointer;
        }

        .form button:hover,
        .form button:active,
        .form button:focus {
            background: #0056b3;
        }

        .form .message {
            margin: 15px 0 0;
            color: #b3b3b3;
            font-size: 12px;
        }
.form .message a {
            color: #0072ff;
            text-decoration: none;
        }

        .form .message a:hover {
            color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="login-page">
        <div class="form">
            <h1>Autentificare</h1>
            <?php if (isset($error)): ?>
                <p style="color: red;"><?php echo $error; ?></p>
            <?php endif; ?>
            <form action="login.php" method="POST">
                <input type="text" name="username" placeholder="Nume utilizator" required>
                <input type="password" name="password" placeholder="Parola" required>
                <button type="submit">Autentificare</button>
                <p class="message">Nu ai cont? <a href="register.php">Inregistrare</a></p>
            </form>
        </div>
    </div>
</body>
</html