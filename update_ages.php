<?php
$file = 'schedule_opd.csv';
$content = file_get_contents($file);
$lines = explode("\n", $content);
$output = [];

foreach ($lines as $line) {
    $trimmed = trim($line);
    if (empty($trimmed)) {
        $output[] = $line;
        continue;
    }
    
    $fields = str_getcsv($trimmed);
    if (count($fields) < 7) {
        $output[] = $line;
        continue;
    }
    
    $opd = $fields[0];
    
    // Add >=15 to 108, ศูนย์สุขภาพ มช., Pain
    if (($opd === '108' || mb_strpos($opd, 'ศูนย์สุขภาพ') !== false || $opd === 'Pain') && empty($fields[4])) {
        $fields[4] = '≥15';
        // Rebuild CSV line
        $parts = [];
        foreach ($fields as $f) {
            if (strpos($f, ',') !== false || strpos($f, '"') !== false) {
                $parts[] = '"' . str_replace('"', '""', $f) . '"';
            } else {
                $parts[] = $f;
            }
        }
        $output[] = implode(',', $parts) . "\r";
    } else {
        $output[] = $line;
    }
}

file_put_contents($file, implode("\n", $output));
echo "Done! Age groups updated.\n";
?>
