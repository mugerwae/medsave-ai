<?php
require_once 'config.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$patient_id = intval($_GET['id']);

$query = "SELECT p.*, h.name as center_name, h.level, h.contact, h.address, h.specialties,
          u.full_name as registered_by_name
          FROM patients p
          LEFT JOIN health_centers h ON p.recommended_center_id = h.id
          LEFT JOIN users u ON p.registered_by = u.id
          WHERE p.id = $patient_id";
$result = mysqli_query($conn, $query);
$patient = mysqli_fetch_assoc($result);

if (!$patient) {
    header('Location: index.php');
    exit;
}

$risk_factors = json_decode($patient['risk_factors'], true);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Details - <?php echo htmlspecialchars($patient['name']); ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container">
        <div class="page-header">
            <h1><i class="fas fa-user-injured"></i> Patient Triage Results</h1>
            <a href="index.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
        </div>

        <div class="urgency-banner urgency-<?php echo strtolower($patient['urgency_level']); ?>">
            <div class="urgency-content">
                <i class="fas fa-exclamation-triangle"></i>
                <div>
                    <h2><?php echo $patient['urgency_level']; ?> PRIORITY</h2>
                    <p>Risk Score: <?php echo $patient['risk_score']; ?>/100</p>
                </div>
            </div>
        </div>

        <div class="details-grid">
            <div class="details-section">
                <h2><i class="fas fa-user"></i> Patient Information</h2>
                <table class="info-table">
                    <tr>
                        <th>Name:</th>
                        <td><?php echo htmlspecialchars($patient['name']); ?></td>
                    </tr>
                    <tr>
                        <th>Age:</th>
                        <td><?php echo $patient['age']; ?> years</td>
                    </tr>
                    <tr>
                        <th>Gestational Age:</th>
                        <td><?php echo $patient['gestational_age']; ?> weeks</td>
                    </tr>
                    <tr>
                        <th>Registered By:</th>
                        <td><?php echo htmlspecialchars($patient['registered_by_name']); ?></td>
                    </tr>
                    <tr>
                        <th>Registration Date:</th>
                        <td><?php echo date('F d, Y H:i', strtotime($patient['created_at'])); ?></td>
                    </tr>
                </table>
            </div>

            <div class="details-section">
                <h2><i class="fas fa-heartbeat"></i> Vital Signs</h2>
                <table class="info-table">
                    <tr>
                        <th>Blood Pressure:</th>
                        <td><?php echo $patient['blood_pressure']; ?></td>
                    </tr>
                    <tr>
                        <th>Temperature:</th>
                        <td><?php echo $patient['temperature']; ?>°C</td>
                    </tr>
                    <tr>
                        <th>Heart Rate:</th>
                        <td><?php echo $patient['heart_rate']; ?> bpm</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="details-section">
            <h2><i class="fas fa-exclamation-circle"></i> Risk Factors Identified</h2>
            <ul class="risk-factors-list">
                <?php foreach ($risk_factors as $factor): ?>
                <li><i class="fas fa-circle"></i> <?php echo htmlspecialchars($factor); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="details-section">
            <h2><i class="fas fa-notes-medical"></i> Clinical Information</h2>
            <div class="clinical-info">
                <div class="info-item">
                    <strong>Current Symptoms:</strong>
                    <p><?php echo nl2br(htmlspecialchars($patient['symptoms'])); ?></p>
                </div>
                <?php if ($patient['medical_history']): ?>
                <div class="info-item">
                    <strong>Medical History:</strong>
                    <p><?php echo nl2br(htmlspecialchars($patient['medical_history'])); ?></p>
                </div>
                <?php endif; ?>
                <?php if ($patient['complications']): ?>
                <div class="info-item">
                    <strong>Previous Complications:</strong>
                    <p><?php echo nl2br(htmlspecialchars($patient['complications'])); ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="details-section referral-section">
            <h2><i class="fas fa-hospital"></i> Recommended Referral Center</h2>
            <div class="referral-card">
                <h3><?php echo htmlspecialchars($patient['center_name']); ?></h3>
                <p class="center-level"><?php echo $patient['level']; ?> Care Facility</p>
                <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($patient['address']); ?></p>
                <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($patient['contact']); ?></p>
                <div class="specialties">
                    <strong>Specialties:</strong>
                    <?php 
                    $specialties = explode(',', $patient['specialties']);
                    foreach ($specialties as $specialty): 
                    ?>
                    <span class="specialty-badge"><?php echo htmlspecialchars(trim($specialty)); ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <?php if ($patient['urgency_level'] == 'EMERGENCY'): ?>
        <div class="details-section alert-section">
            <h2><i class="fas fa-paper-plane"></i> Emergency Alert Status</h2>
            <?php if ($patient['whatsapp_alert_sent']): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <div>
                    <strong>WhatsApp Alert Sent Successfully!</strong>
                    <p>Referral hospital has been notified of incoming patient.</p>
                </div>
            </div>
            <?php else: ?>
            <button onclick="sendAlert(<?php echo $patient_id; ?>)" class="btn btn-danger btn-large">
                <i class="fas fa-paper-plane"></i> Send Emergency WhatsApp Alert
            </button>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <div class="details-section">
            <h2><i class="fas fa-robot"></i> AI Clinical Decision Support</h2>
            <div class="ai-support-box">
                <pre><?php echo htmlspecialchars($patient['ai_clinical_support']); ?></pre>
            </div>
        </div>

        <?php if (!$patient['feedback_provided']): ?>
        <div class="details-section feedback-section">
            <h2><i class="fas fa-chart-line"></i> AI Learning Feedback</h2>
            <p>Was the AI prediction accurate for this case?</p>
            <div class="feedback-buttons">
                <button onclick="submitFeedback(<?php echo $patient_id; ?>, 1)" class="btn btn-success">
                    <i class="fas fa-check"></i> Prediction Correct
                </button>
                <button onclick="submitFeedback(<?php echo $patient_id; ?>, 0)" class="btn btn-danger">
                    <i class="fas fa-times"></i> Needs Adjustment
                </button>
            </div>
        </div>
        <?php else: ?>
        <div class="details-section">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                Feedback already provided: <strong><?php echo $patient['feedback_correct'] ? 'Correct' : 'Needs Adjustment'; ?></strong>
            </div>
        </div>
        <?php endif; ?>

        <div class="action-buttons">
            <a href="index.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fas fa-print"></i> Print Report
            </button>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    <script>
        function submitFeedback(patientId, isCorrect) {
            if (!confirm('Are you sure you want to submit this feedback?')) return;
            
            fetch('api/submit-feedback.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `patient_id=${patientId}&is_correct=${isCorrect}`
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    alert('Feedback submitted successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            });
        }

        function sendAlert(patientId) {
            if (!confirm('Send emergency WhatsApp alert to referral hospital?')) return;
            
            fetch('api/send-alert.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `patient_id=${patientId}`
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    alert('Emergency alert sent successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            });
        }
    </script>
</body>
</html>

