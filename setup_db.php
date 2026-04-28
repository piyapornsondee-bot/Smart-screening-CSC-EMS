<?php
// setup_db.php
$servername = "localhost";
$username = "root";
$password = "";

try {
    // 1. Connect and create database
    $conn = new PDO("mysql:host=$servername", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $sql = "CREATE DATABASE IF NOT EXISTS smart_screening CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    $conn->exec($sql);
    echo "Database created successfully<br>";
    
    $conn->exec("USE smart_screening");
    
    // 2. Create tables
    $sql = "
    CREATE TABLE IF NOT EXISTS schedule_opd (
        id INT AUTO_INCREMENT PRIMARY KEY,
        opd VARCHAR(50),
        day_week VARCHAR(20),
        clinic VARCHAR(100),
        time_slot VARCHAR(50),
        age_group VARCHAR(20),
        tel VARCHAR(100),
        remark TEXT
    );
    
    CREATE TABLE IF NOT EXISTS warning (
        id INT AUTO_INCREMENT PRIMARY KEY,
        clinic VARCHAR(100),
        time_slot VARCHAR(50),
        warning_text TEXT
    );
    
    CREATE TABLE IF NOT EXISTS condition_rule (
        id INT AUTO_INCREMENT PRIMARY KEY,
        opd VARCHAR(50),
        day_week VARCHAR(20),
        clinic VARCHAR(100),
        diagnosis VARCHAR(255)
    );
    ";
    $conn->exec($sql);
    echo "Tables created successfully<br>";
    
    // 3. Empty tables before import
    $conn->exec("TRUNCATE TABLE schedule_opd");
    $conn->exec("TRUNCATE TABLE warning");
    $conn->exec("TRUNCATE TABLE condition_rule");
    
    // 4. Import schedule_opd.csv
    if (($handle = fopen("schedule_opd.csv", "r")) !== FALSE) {
        $header = fgetcsv($handle); // skip header
        $stmt = $conn->prepare("INSERT INTO schedule_opd (opd, day_week, clinic, time_slot, age_group, tel, remark) VALUES (?, ?, ?, ?, ?, ?, ?)");
        while (($data = fgetcsv($handle)) !== FALSE) {
            if (empty(array_filter($data))) continue; // skip empty rows
            $opd = $data[0] ?? '';
            $day = $data[1] ?? '';
            $clinic = $data[2] ?? '';
            $time = $data[3] ?? '';
            $age = $data[4] ?? '';
            $tel = $data[5] ?? '';
            $remark = $data[6] ?? '';
            
            // Normalize age group symbol
            $age = str_replace('≥', '>=', $age);
            
            $stmt->execute([$opd, $day, $clinic, $time, $age, $tel, $remark]);
        }
        fclose($handle);
        echo "Imported schedule_opd.csv<br>";
    }
    
    // 5. Import warning.csv
    if (($handle = fopen("warning.csv", "r")) !== FALSE) {
        $header = fgetcsv($handle);
        $stmt = $conn->prepare("INSERT INTO warning (clinic, time_slot, warning_text) VALUES (?, ?, ?)");
        while (($data = fgetcsv($handle)) !== FALSE) {
            if (empty(array_filter($data))) continue;
            $clinic = $data[0] ?? '';
            $time = $data[1] ?? '';
            $warning_text = $data[2] ?? '';
            
            if ($clinic !== '') {
                $stmt->execute([$clinic, $time, $warning_text]);
            }
        }
        fclose($handle);
        echo "Imported warning.csv<br>";
    }
    
    // 6. Import condition.csv
    if (($handle = fopen("condition.csv", "r")) !== FALSE) {
        $header = fgetcsv($handle);
        $stmt = $conn->prepare("INSERT INTO condition_rule (opd, day_week, clinic, diagnosis) VALUES (?, ?, ?, ?)");
        while (($data = fgetcsv($handle)) !== FALSE) {
            if (empty(array_filter($data))) continue;
            $opd = $data[0] ?? '';
            $day = $data[1] ?? '';
            $clinic = $data[2] ?? '';
            $diagnosis = $data[3] ?? '';
            
            if ($opd !== '' || $diagnosis !== '') {
                $stmt->execute([$opd, $day, $clinic, $diagnosis]);
            }
        }
        fclose($handle);
        echo "Imported condition.csv<br>";
    }
    
    echo "<h2>All data imported successfully!</h2>";
    
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
