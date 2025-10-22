<?php
require_once '../config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$patient_id = intval($_POST['patient_id']);
$is_correct = intval($_POST['is_correct']);

// Update patient record
$update_query = "UPDATE patients SET feedback_provided = 1, feedback_correct = $is_correct WHERE id = $patient_id";
mysqli_query($conn, $update_query);

// Insert feedback record
$insert_query = "INSERT INTO ai_feedback (patient_id, was_correct, health_worker_id) 
                VALUES ($patient_id, $is_correct, {$_SESSION['user_id']})";
mysqli_query($conn, $insert_query);

echo json_encode(['success' => true, 'message' => 'Feedback submitted successfully']);
?>

