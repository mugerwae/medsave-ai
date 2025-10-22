<?php
require_once '../config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$patient_id = intval($_POST['patient_id']);

// Get patient details
$query = "SELECT p.*, h.name as center_name, h.contact 
          FROM patients p 
          LEFT JOIN health_centers h ON p.recommended_center_id = h.id 
          WHERE p.id = $patient_id";
$result = mysqli_query($conn, $query);
$patient = mysqli_fetch_assoc($result);

if (!$patient) {
    echo json_encode(['success' => false, 'message' => 'Patient not found']);
    exit;
}

// Simulate WhatsApp API call
// In production, integrate with WhatsApp Business API
$message = "🚨 URGENT MATERNAL REFERRAL ALERT\n\n";
$message .= "Patient: {$patient['name']}\n";
$message .= "Age: {$patient['age']} years\n";
$message .= "Gestational Age: {$patient['gestational_age']} weeks\n";
$message .= "Urgency: {$patient['urgency_level']}\n\n";
$message .= "Vital Signs:\n";
$message .= "- BP: {$patient['blood_pressure']}\n";
$message .= "- Temp: {$patient['temperature']}°C\n";
$message .= "- HR: {$patient['heart_rate']} bpm\n\n";
$message .= "Referral Center: {$patient['center_name']}\n";
$message .= "Contact: {$patient['contact']}";

// Log for demo purposes
error_log("WhatsApp Alert Sent: " . $message);

// Update patient record
mysqli_query($conn, "UPDATE patients SET whatsapp_alert_sent = 1 WHERE id = $patient_id");

echo json_encode([
    'success' => true, 
    'message' => 'Emergency alert sent successfully',
    'alert_details' => $message
]);
?>
