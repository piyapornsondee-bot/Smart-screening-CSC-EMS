<?php
// suggest.php — returns matching diagnosis/clinic names for autocomplete
header('Content-Type: application/json; charset=utf-8');

$servername = "localhost";
$username = "root";
$password = "";

try {
    $conn = new PDO("mysql:host=$servername;dbname=smart_screening;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $q = $_GET['q'] ?? '';
    if (mb_strlen($q) < 1) {
        echo json_encode([]);
        exit;
    }

    $suggestions = [];

    // 1. Search diagnosis from condition_rule
    $stmt = $conn->prepare("SELECT DISTINCT diagnosis FROM condition_rule WHERE diagnosis LIKE ? AND diagnosis != '' LIMIT 30");
    $stmt->execute(["%$q%"]);
    foreach ($stmt->fetchAll(PDO::FETCH_COLUMN) as $d) {
        $suggestions[] = ['text' => $d, 'type' => 'diagnosis'];
    }

    // 2. Search clinic from schedule_opd
    $stmt = $conn->prepare("SELECT DISTINCT clinic FROM schedule_opd WHERE clinic LIKE ? AND clinic != '' LIMIT 20");
    $stmt->execute(["%$q%"]);
    foreach ($stmt->fetchAll(PDO::FETCH_COLUMN) as $c) {
        $suggestions[] = ['text' => $c, 'type' => 'clinic'];
    }

    echo json_encode($suggestions);

} catch (PDOException $e) {
    echo json_encode([]);
}
?>
