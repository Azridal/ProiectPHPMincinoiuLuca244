  <?php
require 'db.php';
session_start();

// Verificam daca utilizatorul este autentificat si are rol de admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php"); // Redirectioneaza utilizatorii neautorizati
    exit();
}

// Stergerea unui eveniment
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM events WHERE id = ?");
        $stmt->execute([$delete_id]);
        $success = "Evenimentul a fost șters cu succes!";
    } catch (PDOException $e) {
        $error = "Eroare la ștergerea evenimentului: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrare Evenimente</title>
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
            max-width: 1200px;
            margin: auto;
            padding: 20px;
        }

        .event-card {
            background-color: white;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .event-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .event-card-content {
            padding: 20px;
        }

        .event-card-content h3 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }

        .event-card-content p {
            font-size: 14px;
            color: #666;
            margin: 10px 0;
        }

        .event-card-content .event-date {
            font-weight: bold;
            color: #007bff;
        }

        .btn {
            display: inline-block;
            background-color: #ff4d4d;
            color: white;
            padding: 10px 20px;
            margin-top: 10px;
            text-decoration: none;
            border-radius: 4px;
        }

        .btn:hover {
            background-color: #d93636;
        }

        .success, .error {
            text-align: center;
            padding: 10px;
            margin: 10px 0;
            font-weight: bold;
        }

        .success {
            color: #28a745;
        }

        .error {
            color: #dc3545;
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
    <!-- Container pentru administrare -->
    <div class="container">
        <h1>Administrare Evenimente</h1>

        <!-- Mesaje de succes sau eroare -->
        <?php if (isset($success)): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php elseif (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php
        // Preluare toate evenimente din baza de date
        $stmt = $pdo->query("SELECT * FROM events ORDER BY start_date ASC");
        $events = $stmt->fetchAll();

        // Afisam evenimentele
        if (count($events) > 0):
            foreach ($events as $event):
        ?>
        <div class="event-card">
            <img src="<?php echo htmlspecialchars($event['image']); ?>" alt="Imagine Eveniment">
            <div class="event-card-content">
                <h3><?php echo htmlspecialchars($event['title']); ?></h3>
                <p><?php echo htmlspecialchars($event['description']); ?></p>
                <p class="event-date">
                    Începe: <?php echo htmlspecialchars($event['start_date']); ?><br>
                    Se termină: <?php echo htmlspecialchars($event['end_date']); ?>
                </p>
                <p>Locatie:<?php echo htmlspecialchars($event['location']); ?></p>
                <p>Nivel Interes:<?php echo htmlspecialchars($event['interest']); ?></p>
                <a href="admin.php?delete_id=<?php echo $event['id']; ?>" class="btn" onclick="return confirm('Ești sigur că vrei să ștergi acest eveniment?');">Șterge</a>
            </div>
        </div>
        <?php
            endforeach;
        else:
            echo "<p>Nu există evenimente de administrat.</p>";
        endif;
        ?>
    </div>
</body>
</html>
<?php
require 'db.php';

// Verificare daca user-ul este admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Stergerea unui mesaj
if (isset($_GET['delete_message_id'])) {
    $delete_message_id = $_GET['delete_message_id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM contact_messages WHERE id = ?");
        $stmt->execute([$delete_message_id]);
        $success = "Mesajul a fost șters cu succes!";
    } catch (PDOException $e) {
        $error = "A apărut o eroare la ștergerea mesajului.";
    }
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrare</title>
</head>
<body>
    <h1>Administrare</h1>
<!-- Afisare mesaje -->
    <?php if (isset($success)): ?>
        <p style="color: green;"><?php echo $success; ?></p>
    <?php elseif (isset($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <h2>Mesaje de Contact</h2>
    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nume</th>
                <th>Email</th>
                <th>Mesaj</th>
                <th>Data</th>
                <th>Acțiuni</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Preluare mesaje din baza de date
            $stmt = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC");
            $messages = $stmt->fetchAll();

            if (count($messages) > 0):
                foreach ($messages as $message):
            ?>
            <tr>
                <td><?php echo htmlspecialchars($message['id']); ?></td>
                <td><?php echo htmlspecialchars($message['name']); ?></td>
                <td><?php echo htmlspecialchars($message['email']); ?></td>
                <td><?php echo htmlspecialchars($message['message']); ?></td>
                <td><?php echo htmlspecialchars($message['created_at']); ?></td>
                <td>
                    <a href="admin.php?delete_message_id=<?php echo $message['id']; ?>" onclick="return confirm('Ești sigur că vrei să ștergi acest mesaj?');">Șterge</a>
                </td>
            </tr>
            <?php
                endforeach;
            else:
                echo "<tr><td colspan='6'>Nu există mesaje.</td></tr>";
            endif;
            ?>
        </tbody>
    </table>
</body>
</html