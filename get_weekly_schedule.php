<?php
// get_weekly_schedule.php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";

try {
    $conn = new PDO("mysql:host=$servername;dbname=smart_screening;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $opd = $_GET['opd'] ?? '';

    if ($opd === '') {
        echo json_encode(['error' => 'Missing OPD parameter']);
        exit;
    }

    $stmt = $conn->prepare("SELECT * FROM schedule_opd WHERE opd = ? ORDER BY FIELD(day_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'), clinic ASC");
    $stmt->execute([$opd]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $schedule = [
        'Monday' => [],
        'Tuesday' => [],
        'Wednesday' => [],
        'Thursday' => [],
        'Friday' => [],
        'Saturday' => [],
        'Sunday' => []
    ];

    foreach ($rows as $row) {
        $day = $row['day_week'];
        if (isset($schedule[$day])) {
            $schedule[$day][] = [
                'clinic' => $row['clinic'],
                'time_slot' => $row['time_slot'],
                'age_group' => $row['age_group'],
                'remark' => $row['remark']
            ];
        }
    }

    echo json_encode($schedule);

} catch(PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
