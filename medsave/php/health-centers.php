<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$health_centers = mysqli_query($conn, "SELECT * FROM health_centers ORDER BY level DESC, name ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Health Centers - Maternal AI Triage</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container">
        <div class="page-header">
            <h1><i class="fas fa-hospital"></i> Health Centers Network</h1>
        </div>

        <div style="display: grid; gap: 1.5rem;">
            <?php while($center = mysqli_fetch_assoc($health_centers)): ?>
            <div class="details-section">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                    <div>
                        <h2 style="margin-bottom: 0.5rem;"><?php echo htmlspecialchars($center['name']); ?></h2>
                        <span class="badge badge-<?php echo strtolower($center['level']); ?>" style="font-size: 1rem;">
                            <?php echo $center['level']; ?> Care Facility
                        </span>
                    </div>
                    <div style="text-align: right;">
                        <p><i class="fas fa-map-marker-alt"></i> <?php echo $center['distance']; ?></p>
                        <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($center['contact']); ?></p>
                    </div>
                </div>
                
                <p style="color: #666; margin-bottom: 1rem;">
                    <i class="fas fa-building"></i> <?php echo htmlspecialchars($center['address']); ?>
                </p>
                
                <div>
                    <strong style="color: #667eea;">Specialties:</strong>
                    <div style="margin-top: 0.5rem;">
                        <?php 
                        $specialties = explode(',', $center['specialties']);
                        foreach ($specialties as $specialty): 
                        ?>
                        <span class="badge badge-low" style="margin: 0.25rem;">
                            <?php echo htmlspecialchars(trim($specialty)); ?>
                        </span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
