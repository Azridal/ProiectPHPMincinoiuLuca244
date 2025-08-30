  <?php
require 'db.php';
session_start();
?>


<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evenimente</title>
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
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            margin-top: 10px;
            text-decoration: none;
            border-radius: 4px;
        }

        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <!-- Bara de navigare -->
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

    <!-- Container pentru evenimente -->
    <div class="container">
        <h1>Evenimente Active</h1>
        <?php
        // Selectăm evenimentele active din baza de date
        $stmt = $pdo->query("SELECT * FROM events WHERE status = 'active' ORDER BY start_date ASC");
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
            </div>
        </div>
        <script>
        <?php
            endforeach;
        else:
            echo "<p>Nu există evenimente active momentan.</p>";
        endif;
        ?>
    </div>
</body>
</html>
