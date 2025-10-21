<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id = {$_SESSION['user_id']}"));

// Get statistics
$total_patients = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM patients"))['count'];
$emergency_cases = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM patients WHERE urgency_level = 'EMERGENCY'"))['count'];
$medium_cases = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM patients WHERE urgency_level = 'MEDIUM'"))['count'];
$low_cases = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM patients WHERE urgency_level = 'LOW'"))['count'];
$feedback_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM patients WHERE feedback_provided = 1"))['count'];

// Get recent patients
$recent_patients = mysqli_query($conn, "
    SELECT p.*, h.name as center_name 
    FROM patients p 
    LEFT JOIN health_centers h ON p.recommended_center_id = h.id 
    ORDER BY p.created_at DESC 
    LIMIT 10
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maternal AI Triage System - Dashboard</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container">
        <div class="dashboard-header">
            <h1><i class="fas fa-heartbeat"></i> Dashboard</h1>
            <p>Welcome back, <?php echo htmlspecialchars($user['full_name']); ?></p>
        </div>

        <div class="stats-grid">
            <div class="stat-card emergency">
                <div class="stat-icon"><i class="fas fa-exclamation-triangle"></i></div>
                <div class="stat-info">
                    <h3><?php echo $emergency_cases; ?></h3>
                    <p>Emergency Cases</p>
                </div>
            </div>
            
            <div class="stat-card medium">
                <div class="stat-icon"><i class="fas fa-clock"></i></div>
                <div class="stat-info">
                    <h3><?php echo $medium_cases; ?></h3>
                    <p>Medium Priority</p>
                </div>
            </div>
            
            <div class="stat-card low">
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                <div class="stat-info">
                    <h3><?php echo $low_cases; ?></h3>
                    <p>Low Priority</p>
                </div>
            </div>
            
            <div class="stat-card total">
                <div class="stat-icon"><i class="fas fa-users"></i></div>
                <div class="stat-info">
                    <h3><?php echo $total_patients; ?></h3>
                    <p>Total Patients</p>
                </div>
            </div>
        </div>

        <div class="ai-performance">
            <h2><i class="fas fa-chart-line"></i> AI Learning Performance</h2>
            <div class="performance-grid">
                <div class="performance-item">
                    <h3><?php echo $total_patients; ?></h3>
                    <p>Predictions Made</p>
                </div>
                <div class="performance-item">
                    <h3><?php echo $feedback_count; ?></h3>
                    <p>Feedback Received</p>
                </div>
                <div class="performance-item">
                    <h3><?php echo $total_patients > 0 ? round(($feedback_count / $total_patients) * 100) : 0; ?>%</h3>
                    <p>Feedback Rate</p>
                </div>
            </div>
        </div>

        <div class="action-buttons">
            <a href="new-patient.php" class="btn btn-primary btn-large">
                <i class="fas fa-user-plus"></i> Register New Patient
            </a>
            <a href="patients.php" class="btn btn-secondary btn-large">
                <i class="fas fa-list"></i> View All Patients
            </a>
        </div>

        <div class="recent-cases">
            <h2><i class="fas fa-history"></i> Recent Cases</h2>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Patient Name</th>
                            <th>Age</th>
                            <th>Gestational Age</th>
                            <th>Urgency</th>
                            <th>Risk Score</th>
                            <th>Referral Center</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($patient = mysqli_fetch_assoc($recent_patients)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($patient['name']); ?></td>
                            <td><?php echo $patient['age']; ?></td>
                            <td><?php echo $patient['gestational_age']; ?> weeks</td>
                            <td><span class="badge badge-<?php echo strtolower($patient['urgency_level']); ?>">
                                <?php echo $patient['urgency_level']; ?>
                            </span></td>
                            <td><?php echo $patient['risk_score']; ?>/100</td>
                            <td><?php echo htmlspecialchars($patient['center_name']); ?></td>
                            <td><?php echo date('M d, Y H:i', strtotime($patient['created_at'])); ?></td>
                            <td>
                                <a href="view-patient.php?id=<?php echo $patient['id']; ?>" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>

