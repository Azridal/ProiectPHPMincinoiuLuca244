  <?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "sql303.infinityfree.com";
$username = "if0_38068565";
$password = "OYZqSg9HL0I"; 
$dbname = "if0_38068565_evenimente";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection and print errors if any
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Connected successfully!";
?