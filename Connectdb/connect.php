<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$host = "localhost";
$dbname = "coachpro";
$user = "root";
$pass = "";
try {
    $conn = mysqli_connect($host,$user, $pass,$dbname);
} catch (mysqli_sql_exception $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?>
