<?php


function connectToDatabase() {
$host = '192.168.100.2';
$db   = 'smsdb';
$user = 'mixsimverify';
$pass = 'mix@123123';
$charset = 'utf8';
$port = "3308";

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";


    try {
        $pdo = new PDO($dsn, $user, $pass);
        return $pdo;
    } catch (PDOException $e) {
        if ($e->getCode() == 2006) {
            // MySQL server has gone away, attempt to reconnect
            $pdo = reconnectToDatabase($dsn, $user, $pass);
            return $pdo;
        } else {
            // Handle other exceptions
            throw $e;
        }
    }
}

function reconnectToDatabase($dsn, $username, $password) {
    $maxAttempts = 3;
    $attempts = 0;

    do {
        // Add delay before attempting to reconnect (e.g., sleep for a few seconds)
        sleep(3);

        try {
            $pdo = new PDO($dsn, $username, $password);
            return $pdo;
        } catch (PDOException $e) {
            // Handle any connection errors during reconnection attempts
            $attempts++;
        }
    } while ($attempts < $maxAttempts);

    // If all reconnection attempts fail, throw an exception
    throw new Exception("Unable to reconnect to the database after multiple attempts");
}

// Example usage
try {
    $pdo = connectToDatabase();
    // Perform database operations using $pdo
} catch (Exception $e) {
    // Handle exceptions thrown during the connection or database operations
    echo "Error: " . $e->getMessage();
}
