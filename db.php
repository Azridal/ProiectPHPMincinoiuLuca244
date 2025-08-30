  <?php
// Activare raportare erori pentru debugging
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configurare conexiune MySQL
$servername = "sql303.infinityfree.com";
$username = "if0_38068565";
$password = "OYZqSg9HL0I";
$dbname = "if0_38068565_evenimente";


try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("❌ Connection failed: " . $e->getMessage());
}

?