<?php
// directory.php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "smart_screening";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    // Silently fail if DB not setup yet, but we'll use hardcoded data for the directory mostly
}

$opd_directory = [
    ["OPD 1", "35735, 36335"],
    ["OPD 2", "35736, 35739"],
    ["OPD 3", "35741, 36341"],
    ["OPD 4", "35742, 35720"],
    ["OPD 5", "35743"],
    ["OPD 6", "35745, 36692"],
    ["OPD 7", "35748, 36680"],
    ["OPD Retina", "35903, 35910"],
    ["OPD 9", "35740, 35751"],
    ["OPD 10", "35750, 35760"],
    ["OPD 20", "34436, 36366"],
    ["OPD 21", "35752, 36752"],
    ["OPD 22", "35753, 36353"],
    ["OPD 23", "35754, 36354"],
    ["OPD 24", "35755, 36755"],
    ["จิตเวชเด็ก", "35960"],
    ["OPD 25", "35699, 36399"],
    ["OPD 26", "35756, 36766"],
    ["OPD 27", "35757"],
    ["Ped Cardio", "35419"],
    ["OPD 28", "35758, 36358"],
    ["OPD 29", "35759"],
    ["OPD 101", "35728, 35729"],
    ["OPD 110", "34605, 35726"],
    ["OPD 108", "35170, 36182"],
    ["OPD RT", "34430, 35492"],
    ["Med Non", "36711, 36713"],
    ["Fam med", "36363"],
    ["Pain Clinic", "36379"],
    ["EID", "38589, 38591"],
    ["EID - TB", "38723"],
    ["ดื้อยา", "36338"],
    ["ศูนย์สุขภาพมช.", "43181"],
    ["OPD สงฆ์", "35966"],
    ["Ostomy", "36527"],
    ["ทำแผล", "34408"],
    ["ฉีดยา", "35734"],
    ["นิติเวชชั้น 3", "34433"],
    ["Triage", "35677"],
    ["ER", "36333"],
    ["Observe", "36334, 35737"],
    ["เวชระเบียน", "35636, 36647"],
    ["DNA", "34560"],
    ["ICWN", "35714, 35724"],
    ["Telemed", "34160"]
];

$other_directory = [
    ["ศูนย์รับเงิน 1", "35249, 35250"],
    ["ศูนย์รับเงิน 2", "35247, 35248"],
    ["ศูนย์รับเงิน 3", "36531"],
    ["บัตรทอง OPD", "36535, 36577"],
    ["บัตรทอง IPD", "35602"],
    ["ประกันสังคม", "35181, 36181"],
    ["สังคมสงเคราะห์", "35650"],
    ["ศูนย์จองห้อง", "36385, 34489"],
    ["ห้องยา 30", "35616, 36316"],
    ["ห้องยา 16", "36530"],
    ["ศูนย์ส่งยา ปณ.", "34005"],
    ["อุปกรณ์ฝากขาย", "34428, 34429"],
    ["Lab center", "35645"],
    ["ศูนย์รับ Lab", "36240, 36244"],
    ["X-ray 33", "35453, 35454"],
    ["นัดคิว CT", "36198"],
    ["นัดคิว MMG", "36554"],
    ["นัดคิว U/S", "36707"],
    ["ศูนย์สร้างเสริม", "35710, 36561"],
    ["สำนักงาน OPD", "35731"],
    ["ฝ่ายการ", "35721"],
    ["ขอประวัติ", "35601"],
    ["สนง.ผอ.", "36155"],
    ["สนง.คณบดี", "36144"],
    ["IT", "35919"],
    ["ช่างบันไดเลื่อน", "36218"],
    ["รปภ.", "35500, 38700"],
    ["เปล", "35692"],
    ["บ้านพักญาติ", "34738"]
];

$affiliate_directory = [
    ["ศรีพัฒน์", "36900, 36901"],
    ["ศูนย์ความเป็นเลิศ", "34700, 34701"],
    ["GMC", "20666"],
    ["มีบุตรยาก", "34714, 34715"]
];

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สมุดเบอร์โทรศัพท์ - Smart Screening</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Thai:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .directory-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
            margin-bottom: 40px;
            margin-top: 20px;
        }

        .directory-section {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 1.25rem;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.05);
            overflow: hidden;
            height: fit-content;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .directory-section:hover {
            transform: translateY(-8px);
            border-color: #79d2bd;
            box-shadow: 0 20px 40px rgba(74, 157, 134, 0.12);
        }

        .directory-header {
            background: linear-gradient(135deg, #e0f2f1, #b2dfdb);
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .directory-header i {
            color: #00796b;
            font-size: 1.25rem;
        }

        .directory-header h3 {
            font-family: 'IBM Plex Sans Thai', sans-serif;
            font-size: 1.1rem;
            font-weight: 700;
            color: #004d40;
            margin: 0;
        }

        .directory-table {
            width: 100%;
            border-collapse: collapse;
        }

        .directory-table tr {
            border-bottom: 1px solid rgba(74, 157, 134, 0.05);
            transition: background 0.2s;
        }

        .directory-table tr:hover {
            background: rgba(255, 255, 255, 0.5);
        }

        .directory-table tr:last-child {
            border-bottom: none;
        }

        .directory-table td {
            padding: 1rem 1.5rem;
            font-size: 0.95rem;
        }

        .dept-col {
            font-family: 'IBM Plex Sans Thai', sans-serif;
            color: #444;
            font-weight: 600;
            width: 55%;
        }

        .phone-col {
            text-align: right;
            font-family: 'Inter', sans-serif;
            color: #2d8686;
            font-weight: 700;
            white-space: nowrap;
        }

        .directory-search-container {
            margin-bottom: 2rem;
            max-width: 600px;
        }

        .directory-search-wrapper {
            position: relative;
        }

        .directory-search-wrapper i {
            position: absolute;
            left: 1.25rem;
            top: 50%;
            transform: translateY(-50%);
            color: #888;
        }

        .directory-search-input {
            width: 100%;
            padding: 1rem 1rem 1rem 3rem;
            border-radius: 1rem;
            border: 1px solid rgba(0, 0, 0, 0.05);
            background: white;
            font-size: 1rem;
            outline: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
        }

        .directory-search-input:focus {
            box-shadow: 0 8px 24px rgba(74, 157, 134, 0.15);
            border-color: #79d2bd;
        }

        @media (max-width: 1100px) {
            .directory-container {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .directory-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header class="top-bar">
        <div class="top-bar-inner">
            <div class="top-bar-left">
                <div class="brand">
                    <i class="fa-solid fa-phone-flip"></i>
                    <h1>สมุดเบอร์โทรศัพท์</h1>
                </div>
            </div>
            <div class="top-bar-right">
                <div class="nav-links">
                    <a href="index.php" class="nav-link">
                        <i class="fas fa-search"></i> คัดกรอง
                    </a>
                    <a href="summary.php" class="nav-link">
                        <i class="fas fa-th-list"></i> ตารางรวมทุก OPD
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main id="app">
        <div class="directory-search-container">
            <div class="directory-search-wrapper">
                <i class="fas fa-filter"></i>
                <input type="text" id="directory-search" class="directory-search-input" placeholder="ค้นหาหน่วยงาน หรือ เบอร์โทรศัพท์...">
            </div>
        </div>

        <div class="directory-container">
            <!-- Section 1: OPD -->
            <section class="directory-section" data-section="opd">
                <div class="directory-header">
                    <i class="fas fa-hospital-user"></i>
                    <h3>เบอร์โทรภายใน OPD</h3>
                </div>
                <div class="directory-table-wrapper">
                    <table class="directory-table">
                        <tbody id="opd-list">
                            <?php foreach ($opd_directory as $item): ?>
                            <tr>
                                <td class="dept-col"><?php echo $item[0]; ?></td>
                                <td class="phone-col"><?php echo $item[1]; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Section 2: Other -->
            <section class="directory-section" data-section="other">
                <div class="directory-header">
                    <i class="fas fa-building"></i>
                    <h3>หน่วยงานอื่นๆ</h3>
                </div>
                <div class="directory-table-wrapper">
                    <table class="directory-table">
                        <tbody id="other-list">
                            <?php foreach ($other_directory as $item): ?>
                            <tr>
                                <td class="dept-col"><?php echo $item[0]; ?></td>
                                <td class="phone-col"><?php echo $item[1]; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Section 3: Affiliates -->
            <section class="directory-section" data-section="affiliate">
                <div class="directory-header">
                    <i class="fas fa-star"></i>
                    <h3>โรงพยาบาลในเครือ / ศูนย์พิเศษ</h3>
                </div>
                <div class="directory-table-wrapper">
                    <table class="directory-table">
                        <tbody id="affiliate-list">
                            <?php foreach ($affiliate_directory as $item): ?>
                            <tr>
                                <td class="dept-col"><?php echo $item[0]; ?></td>
                                <td class="phone-col"><?php echo $item[1]; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </main>

    <script>
        document.getElementById('directory-search').addEventListener('input', function(e) {
            const term = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('.directory-table tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(term)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });

            // Hide empty sections
            document.querySelectorAll('.directory-section').forEach(section => {
                const visibleRows = section.querySelectorAll('tr:not([style*="display: none"])');
                if (visibleRows.length === 0) {
                    section.style.display = 'none';
                } else {
                    section.style.display = '';
                }
            });
        });
    </script>
</body>
</html>
