<?php




// Function to backup data to JSON file
function backupDataToJSON($table, $filename)
{
    require '/var/www/smsmarket/html/backend/config.php';
    echo "backup $table to $filename\n\n";
    $stmt = $pdo->prepare("SELECT * FROM $table");
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    file_put_contents($filename, json_encode($data, JSON_PRETTY_PRINT));
}

// Backup users and cms_users every hour
// Adjust table names and filenames as needed
$backupInterval = 3600; // 1 hour


// Output directory
$outputBaseDir = '/root/backups/';

// Ensure the base output directory exists
if (!is_dir($outputBaseDir)) {
    mkdir($outputBaseDir, 0755, true);
}

while (true) {
    // Get the current date
    $currentDate = date('Y-m-d');

    // Create a daily backup folder
    $outputDir = $outputBaseDir . $currentDate . '/';
    if (!is_dir($outputDir)) {
        mkdir($outputDir, 0755, true);
    }

    // Backup files with timestamps
    $timestamp = date('H-i-s');
    $usersFilename = $outputDir . "users_$timestamp.json";
    $cmsUsersFilename = $outputDir . "cms_users_$timestamp.json";

    backupDataToJSON('users', $usersFilename);
    backupDataToJSON('cms_users', $cmsUsersFilename);

    sleep($backupInterval);
}

// This script runs indefinitely, so you may want to handle stopping it appropriately.

?>