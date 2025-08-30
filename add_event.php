  <?php
require 'db.php';
session_start();

// Verificare utilizatorul este autentificat
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Procesarea formularului pentru adaugarea unui eveniment
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $title = htmlspecialchars($_POST['title']);
    $description = htmlspecialchars($_POST['description']);
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $image = $_FILES['image'];
    $location = $_POST['location'];

    // Validare simpla
    if (empty($title) || empty($description) || empty($start_date) || empty($end_date)) {
        $error = "Toate câmpurile sunt obligatorii.";
    } elseif ($image['error'] !== UPLOAD_ERR_OK) {
        $error = "Eroare la încărcarea imaginii.";
    } else {
        // Salvare imagine în directorul "img/"
        $imagePath = 'img/' . basename($image['name']);
        if (move_uploaded_file($image['tmp_name'], $imagePath)) {
            try {
                // Inserare eveniment in baza de date
                $stmt = $pdo->prepare("
                    INSERT INTO events (user_id, title, description, start_date, end_date, image, location)
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([$user_id, $title, $description, $start_date, $end_date, $imagePath, $location]);
                $success = "Evenimentul a fost adăugat cu succes!";
            } catch (PDOException $e) {
                $error = "Eroare la salvarea evenimentului: " . $e->getMessage();
            }
        } else {
            $error = "Nu s-a putut salva imaginea.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adaugă Eveniment</title>
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

        form input, form textarea, form button {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
            width: 100%;
            box-sizing: border-box;
        }

        form textarea {
            resize: vertical;
        }

        form button {
            background-color: #007bff;
            color: white;
            border: none;
            text-transform: uppercase;
            cursor: pointer;
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
        <h1>Adauga Eveniment</h1>
        <p>Completeaza formularul pentru a adauga un eveniment nou.</p>

        <!-- Mesaje de succes sau eroare -->
        <div class="message">
            <?php if (isset($error)): ?>
                <p class="error"><?php echo $error; ?></p>
            <?php elseif (isset($success)): ?>
                <p class="success"><?php echo $success; ?></p>
            <?php endif; ?>
        </div>

        <!-- Formular -->
        <form action="add_event.php" method="POST" enctype="multipart/form-data">
            <label for="title">Titlu:</label>
            <input type="text" id="title" name="title" required>

            <label for="description">Descriere:</label>
            <textarea id="description" name="description" rows="5" required></textarea>

            <label for="start_date">Data inceput:</label>
            <input type="datetime-local" id="start_date" name="start_date" required>

            <label for="end_date">Data sfarsit:</label>
            <input type="datetime-local" id="end_date" name="end_date" required>

            <label for="image">Imagine:</label>
            <input type="file" id="image" name="image" accept="image/*" required>

            <label for="location">Locatie:</label>
            <input type="text" id="location" name="location" required>

            <button type="submit">Adaugă Eveniment</button>
        </form>
    </div>
</body>
</html>