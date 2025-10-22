<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$urgency_filter = isset($_GET['urgency']) ? mysqli_real_escape_string($conn, $_GET['urgency']) : '';

$where_clause = "WHERE 1=1";
if ($search) {
    $where_clause .= " AND p.name LIKE '%$search%'";
}
if ($urgency_filter) {
    $where_clause .= " AND p.urgency_level = '$urgency_filter'";
}

$query = "SELECT p.*, h.name as center_name 
          FROM patients p 
          LEFT JOIN health_centers h ON p.recommended_center_id = h.id 
          $where_clause
          ORDER BY p.created_at DESC";
$patients = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Patients - Maternal AI Triage</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container">
        <div class="page-header">
            <h1><i class="fas fa-users"></i> All Patients</h1>
        </div>

        <div class="filters-section" style="background: white; padding: 1.5rem; border-radius: 10px; margin-bottom: 2rem; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
            <form method="GET" style="display: flex; gap: 1rem; align-items: end;">
                <div class="form-group" style="flex: 1; margin: 0;">
                    <label for="search">Search Patient</label>
                    <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Enter patient name">
                </div>
                <div class="form-group" style="flex: 1; margin: 0;">
                    <label for="urgency">Filter by Urgency</label>
                    <select id="urgency" name="urgency">
                        <option value="">All Levels</option>
                        <option value="EMERGENCY" <?php echo $urgency_filter == 'EMERGENCY' ? 'selected' : ''; ?>>Emergency</option>
                        <option value="MEDIUM" <?php echo $urgency_filter == 'MEDIUM' ? 'selected' : ''; ?>>Medium</option>
                        <option value="LOW" <?php echo $urgency_filter == 'LOW' ? 'selected' : ''; ?>>Low</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Search
                </button>
                <a href="patients.php" class="btn btn-secondary">
                    <i class="fas fa-redo"></i> Reset
                </a>
            </form>
        </div>

        <div class="recent-cases">
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
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
                        <?php while($patient = mysqli_fetch_assoc($patients)): ?>
                        <tr>
                            <td><?php echo $patient['id']; ?></td>
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

