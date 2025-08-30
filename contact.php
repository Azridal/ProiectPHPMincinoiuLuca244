  <?php
require 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $message = htmlspecialchars($_POST['message']);

    // Validare de baza
    if (empty($name) || empty($email) || empty($message)) {
        $error = "Toate câmpurile sunt obligatorii.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Adresa de email nu este validă.";
    } else {
        try {
            // Salvare mesaj in baza de date
            $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)");
            $stmt->execute([$name, $email, $message]);
            $success = "Mesajul tău a fost trimis cu succes!";
        } catch (PDOException $e) {
            $error = "A apărut o eroare la salvarea mesajului.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }

        .navbar {
            background-color: #333;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            margin: 0 10px;
            font-size: 16px;
        }

        .navbar a:hover {
            text-decoration: underline;
        }

        .container {
            max-width: 800px;
            margin: auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #333;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        form label {
            margin: 10px 0 5px;
            color: #333;
            font-weight: bold;
        }

        form input, form textarea {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
            width: 100%;
            box-sizing: border-box;
        }

        form button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            text-transform: uppercase;
        }

        form button:hover {
            background-color: #0056b3;
        }

        .message {
            margin-top: 15px;
            text-align: center;
        }

        .message p {
            margin: 0;
        }

        .message p.error {
            color: red;
        }

        .message p.success {
            color: green;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div>
            <a href="index.php">Acasa</a>
            <a href="contact.php">Contact</a>
            <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] == 'admin'): ?>
                <a href="add_event.php">Adauga Eveniment</a>
            <?php endif; ?>
            <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] == 'admin'): ?>
                <a href="admin.php">Administrare</a>
            <?php endif; ?>
        </div>
        <div>
            <?php if (!empty($_SESSION['user_id'])): ?>
                <a href="account.php">Contul meu</a>
            <?php endif; ?>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="logout.php">Delogare</a>
            <?php else: ?>
                <a href="login.php">Logare</a>
            <?php endif; ?>
        </div>
    </div>
    <!-- Container -->
    <div class="container">
        <h1>Contacteaza-ne</h1>
        <p>Completeaza formularul de mai jos pentru a ne trimite un mesaj.</p>

        <!-- Mesaje de succes sau eroare -->
        <div class="message">
            <?php if (isset($error)): ?>
                <p class="error"><?php echo $error; ?></p>
            <?php elseif (isset($success)): ?>
                <p class="success"><?php echo $success; ?></p>
            <?php endif; ?>
        </div>

        <!-- Formular -->
        <form action="contact.php" method="POST">
            <label for="name">Nume:</label>
            <input type="text" id="name" name="name" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="message">Mesaj:</label>
            <textarea id="message" name="message" rows="5" required></textarea>

            <button type="submit">Trimite</button>
        </form>
    </div>
</body>
</html