<?php 
session_start();
if (!isset($_SESSION['name']) || !isset($_SESSION['level']) || $_SESSION['level'] != "rootlevel") {
  header('Location:https://' . $_SERVER["SERVER_NAME"] . '/404.php', true, 301);
  die();
}

$targetDate = isset($_POST['date']) ? $_POST['date'] : '';
try {
  require_once '/var/www/smsmarket/html/backend/config.php';
  // $formattedDate = DateTime::createFromFormat('d-m-Y', $targetDate)->format('Y-m-d');
  $sql = "SELECT id,ip, country, user_id, email, cpanel, state, created_at FROM login_log WHERE DATE(created_at) = :formattedDate ORDER BY id DESC";
  $stmt = $pdo->prepare($sql);
  $stmt->bindParam(':formattedDate', $targetDate, PDO::PARAM_STR);
  $stmt->execute();
  
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
  echo json_encode($result);
} catch (Exception $e) {
  echo json_encode(['error' => $e->getMessage()]);
}
