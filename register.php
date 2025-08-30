  <?php
require 'db.php';
session_start();

// Prevenirea accesului utilizatorilor autentificati
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Procesarea inregitrarii
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    try {
        // Adauga utilizatorul in baza de date
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$username, $email, $password]);

        // Redirectionare la pagina de log in 
        header("Location: login.php");
        exit();
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) { // Eroare daca utilizatorul deja exista
            echo "<p style='color: red;'>Numele de utilizator sau email-ul există deja!</p>";
        } else {
            echo "<p style='color: red;'>Eroare la înregistrare: " . $e->getMessage() . "</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Înregistrare</title>
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
            <h1>Inregistrare</h1>
            <form action="register.php" method="POST">
                <input type="text" name="username" placeholder="Nume utilizator" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Parola" required>
                <button type="submit">Inregistreaza-te</button>
                <p class="message">Ai deja un cont? <a href="login.php">Autentificare</a></p>
            </form>
        </div>
    </div>
</body>
</html