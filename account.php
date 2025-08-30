 <?php
// account.php – pagină cont: modificare username, email, parolă
declare(strict_types=1);
require 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}
function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

$userId = (int)$_SESSION['user_id'];

// Citește utilizatorul curent
$stmt = $pdo->prepare("SELECT id, username, email, password FROM users WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$user) {
    // sesiune coruptă
    session_destroy();
    header('Location: login.php');
    exit;
}

$messages = [];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf = $_POST['csrf'] ?? '';
    if (!hash_equals($_SESSION['csrf'], $csrf)) {
        http_response_code(400);
        $errors[] = 'Sesiune invalidă (CSRF). Reîncarcă pagina și încearcă din nou.';
    } else {
        $newUsername = trim((string)($_POST['username'] ?? ''));
        $newEmail    = trim((string)($_POST['email'] ?? ''));
        $curPass     = (string)($_POST['current_password'] ?? '');
        $newPass     = (string)($_POST['new_password'] ?? '');
        $newPass2    = (string)($_POST['new_password_confirm'] ?? '');

        // Pentru orice modificare cerem parola curentă
        if ($newUsername !== $user['username'] || $newEmail !== $user['email'] || $newPass !== '') {
            if ($curPass === '' || !password_verify($curPass, $user['password'])) {
                $errors[] = 'Parola curentă este incorectă.';
            }
        }

        // Validări bazice
        if ($newUsername === '') $errors[] = 'Username-ul nu poate fi gol.';
        if ($newUsername !== '' && (strlen($newUsername) < 3 || strlen($newUsername) > 50)) {
            $errors[] = 'Username-ul trebuie să aibă între 3 și 50 de caractere.';
        }
        if ($newEmail === '' || !filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email invalid.';
        }

        // Dacă vrea schimbare parolă
        $updatePassword = false;
        $newHash = null;
        if ($newPass !== '') {
            if ($newPass !== $newPass2) $errors[] = 'Parolele noi nu coincid.';
            if (!$errors) {
                $newHash = password_hash($newPass, PASSWORD_DEFAULT);
                $updatePassword = true;
            }
        }

        // Unicitate username/email
        if (!$errors && $newUsername !== $user['username']) {
            $st = $pdo->prepare("SELECT id FROM users WHERE username = :u AND id <> :id LIMIT 1");
            $st->execute([':u' => $newUsername, ':id' => $userId]);
            if ($st->fetch()) $errors[] = 'Acest username este deja folosit.';
        }
        if (!$errors && $newEmail !== $user['email']) {
            $st = $pdo->prepare("SELECT id FROM users WHERE email = :e AND id <> :id LIMIT 1");
            $st->execute([':e' => $newEmail, ':id' => $userId]);
            if ($st->fetch()) $errors[] = 'Acest email este deja folosit.';
        }

        if (!$errors) {
            $pdo->beginTransaction();
            try {
                if ($updatePassword) {
                    $st = $pdo->prepare("UPDATE users SET username = :u, email = :e, password = :p WHERE id = :id");
                    $st->execute([':u'=>$newUsername, ':e'=>$newEmail, ':p'=>$newHash, ':id'=>$userId]);
                } else {
                    $st = $pdo->prepare("UPDATE users SET username = :u, email = :e WHERE id = :id");
                    $st->execute([':u'=>$newUsername, ':e'=>$newEmail, ':id'=>$userId]);
                }
                $pdo->commit();

                // Actualizează sesiunea
                $_SESSION['username'] = $newUsername;
                $_SESSION['email']    = $newEmail;

                $messages[] = 'Profil actualizat cu succes.';

                // reîncarcă userul pentru a reflecta noile valori
                $stmt = $pdo->prepare("SELECT id, username, email, password FROM users WHERE id = :id LIMIT 1");
                $stmt->execute([':id' => $userId]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
            } catch (Throwable $ex) {
                $pdo->rollBack();
                $errors[] = 'A apărut o eroare la salvare. Încearcă din nou.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contul meu</title>
  <style>
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
    body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;background:#f6f7fb;margin:0;padding:0}
    .wrap{max-width:760px;margin:40px auto;padding:24px;background:#fff;border-radius:14px;box-shadow:0 6px 24px rgba(0,0,0,.08)}
    h1{margin:0 0 16px;font-size:24px}
    .note{padding:12px 14px;border-radius:10px;margin:12px 0}
    .ok{background:#e8f6ef;border:1px solid #b7ead1}
    .err{background:#fdecea;border:1px solid #f5c2c0}
    form{display:grid;gap:12px;margin-top:8px}
    label{font-weight:600}
    input[type=text], input[type=email], input[type=password]{width:100%;padding:10px;border:1px solid #d4d8e1;border-radius:10px;box-sizing:border-box}
    .row{display:grid;grid-template-columns:1fr 1fr;gap:12px}
    .actions{display:flex;gap:10px;align-items:center}
    button{padding:10px 14px;border:0;border-radius:10px;background:#4f46e5;color:#fff;font-weight:700;cursor:pointer}
    a.btn{display:inline-block;padding:10px 14px;border-radius:10px;background:#e5e7eb;color:#111;text-decoration:none}
    small{color:#6b7280}
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
  <div class="wrap">
    <h1>Contul meu</h1>

    <?php foreach ($messages as $m): ?>
      <div class="note ok"><?=h($m)?></div>
    <?php endforeach; ?>
    <?php foreach ($errors as $e): ?>
      <div class="note err"><?=h($e)?></div>
    <?php endforeach; ?>

    <form method="post" autocomplete="off" novalidate>
      <input type="hidden" name="csrf" value="<?=h($_SESSION['csrf'])?>">
      <div class="row">
        <div>
          <label for="username">Username</label>
          <input type="text" id="username" name="username" required value="<?=h($user['username'])?>">
        </div>
        <div>
          <label for="email">Email</label>
          <input type="email" id="email" name="email" required value="<?=h($user['email'])?>">
        </div>
      </div>

      <div class="row">
        <div>
          <label for="current_password">Parola curentă <small>(necesară pentru orice modificare)</small></label>
          <input type="password" id="current_password" name="current_password" required>
        </div>
        <div></div>
      </div>

      <div class="row">
        <div>
          <label for="new_password">Parolă nouă <small>(opțional)</small></label>
          <input type="password" id="new_password" name="new_password" placeholder="Lasă gol dacă nu schimbi">
        </div>
        <div>
          <label for="new_password_confirm">Confirmă parola nouă</label>
          <input type="password" id="new_password_confirm" name="new_password_confirm" placeholder="Repetă parola nouă">
        </div>
      </div>

      <div class="actions">
        <button type="submit">Salvează modificările</button>
        <a class="btn" href="index.php">Înapoi la evenimente</a>
        <a class="btn" href="logout.php">Delogare</a>
      </div>
    </form>
  </div>
</body>
</html>