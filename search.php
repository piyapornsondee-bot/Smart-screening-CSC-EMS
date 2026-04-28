<?php
// search.php
$servername = "localhost";
$username = "root";
$password = "";

header('Content-Type: application/json');

try {
    $conn = new PDO("mysql:host=$servername;dbname=smart_screening;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $age = $_POST['age'] ?? ''; 
    $day = $_POST['day'] ?? ''; 
    $keyword = $_POST['keyword'] ?? '';

    $results = [];

    if ($keyword !== '') {
        // ===== KEYWORD SEARCH MODE =====
        
        // Step 1: Search from condition_rule table
        $stmt = $conn->prepare("SELECT * FROM condition_rule WHERE diagnosis LIKE ? OR clinic LIKE ?");
        $stmt->execute(["%$keyword%", "%$keyword%"]);
        $conditions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($conditions as $cond) {
            $cond_opd = $cond['opd'];
            $cond_clinic = $cond['clinic'];
            $cond_day = $cond['day_week'];
            
            // If condition specifies a day, and user selected a day that doesn't match, skip
            if ($day !== '' && $cond_day !== '' && strtolower($cond_day) !== strtolower($day)) {
                continue; 
            }
            
            // Build schedule query dynamically based on what filters are provided
            $sched_sql = "SELECT * FROM schedule_opd WHERE 1=1";
            $sched_params = [];
            
            if ($age !== '') {
                $sched_sql .= " AND age_group = ?";
                $sched_params[] = $age;
            }
            if ($day !== '') {
                $sched_sql .= " AND day_week = ?";
                $sched_params[] = $day;
            } elseif ($cond_day !== '') {
                $sched_sql .= " AND day_week = ?";
                $sched_params[] = $cond_day;
            }
            if ($cond_opd !== '') {
                $sched_sql .= " AND opd = ?";
                $sched_params[] = $cond_opd;
            }
            if ($cond_clinic !== '') {
                $sched_sql .= " AND clinic = ?";
                $sched_params[] = $cond_clinic;
            }
            
            $stmt2 = $conn->prepare($sched_sql);
            $stmt2->execute($sched_params);
            $schedules = $stmt2->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($schedules as $sched) {
                $stmt3 = $conn->prepare("SELECT warning_text FROM warning WHERE clinic = ?");
                $stmt3->execute([$sched['clinic']]);
                $warnings = $stmt3->fetchAll(PDO::FETCH_COLUMN);
                
                $sched['diagnosis_matched'] = $cond['diagnosis'];
                $sched['warnings'] = $warnings;
                
                $key = $sched['opd'] . '_' . $sched['clinic'] . '_' . $sched['day_week'] . '_' . $sched['time_slot'] . '_' . $sched['age_group'];
                if (!isset($results[$key])) {
                    $results[$key] = $sched;
                }
            }
        }
        
        // Step 2: Direct clinic match from schedule_opd
        $sched_sql = "SELECT * FROM schedule_opd WHERE (clinic LIKE ? OR remark LIKE ?)";
        $sched_params = ["%$keyword%", "%$keyword%"];
        
        if ($age !== '') {
            $sched_sql .= " AND age_group = ?";
            $sched_params[] = $age;
        }
        if ($day !== '') {
            $sched_sql .= " AND day_week = ?";
            $sched_params[] = $day;
        }
        
        $stmt2 = $conn->prepare($sched_sql);
        $stmt2->execute($sched_params);
        $schedules = $stmt2->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($schedules as $sched) {
            $stmt3 = $conn->prepare("SELECT warning_text FROM warning WHERE clinic = ?");
            $stmt3->execute([$sched['clinic']]);
            $warnings = $stmt3->fetchAll(PDO::FETCH_COLUMN);
            
            $sched['diagnosis_matched'] = $sched['clinic'];
            $sched['warnings'] = $warnings;
            
            $key = $sched['opd'] . '_' . $sched['clinic'] . '_' . $sched['day_week'] . '_' . $sched['time_slot'] . '_' . $sched['age_group'];
            if (!isset($results[$key])) {
                $results[$key] = $sched;
            }
        }
        
    } else {
        // ===== NO KEYWORD: list all clinics for selected day/age =====
        $sched_sql = "SELECT * FROM schedule_opd WHERE 1=1";
        $sched_params = [];
        
        if ($age !== '') {
            $sched_sql .= " AND age_group = ?";
            $sched_params[] = $age;
        }
        if ($day !== '') {
            $sched_sql .= " AND day_week = ?";
            $sched_params[] = $day;
        }
        
        // Must have at least one filter to avoid dumping entire DB
        if ($age === '' && $day === '') {
            echo json_encode([]);
            exit;
        }
        
        $stmt2 = $conn->prepare($sched_sql);
        $stmt2->execute($sched_params);
        $schedules = $stmt2->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($schedules as $sched) {
            $stmt3 = $conn->prepare("SELECT warning_text FROM warning WHERE clinic = ?");
            $stmt3->execute([$sched['clinic']]);
            $warnings = $stmt3->fetchAll(PDO::FETCH_COLUMN);
            
            $sched['diagnosis_matched'] = '-';
            $sched['warnings'] = $warnings;
            
            $key = $sched['opd'] . '_' . $sched['clinic'] . '_' . $sched['day_week'] . '_' . $sched['time_slot'] . '_' . $sched['age_group'];
            if (!isset($results[$key])) {
                $results[$key] = $sched;
            }
        }
    }

    // Sort results by day order then OPD
    $dayOrder = ['Monday' => 1, 'Tuesday' => 2, 'Wednesday' => 3, 'Thursday' => 4, 'Friday' => 5];
    $output = array_values($results);
    usort($output, function($a, $b) use ($dayOrder) {
        $da = $dayOrder[$a['day_week']] ?? 9;
        $db = $dayOrder[$b['day_week']] ?? 9;
        if ($da !== $db) return $da - $db;
        return strcmp($a['opd'], $b['opd']);
    });

    echo json_encode($output);

} catch(PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
