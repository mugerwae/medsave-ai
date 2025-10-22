<?php if (!isset($_SESSION['user_id'])) return; ?>
<header class="main-header">
    <div class="header-container">
        <div class="logo">
            <i class="fas fa-heartbeat"></i>
            <span>Maternal AI Triage</span>
        </div>
        <nav class="main-nav">
            <a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
                <i class="fas fa-home"></i> Dashboard
            </a>
            <a href="new-patient.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'new-patient.php' ? 'active' : ''; ?>">
                <i class="fas fa-user-plus"></i> New Patient
            </a>
            <a href="patients.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'patients.php' ? 'active' : ''; ?>">
                <i class="fas fa-users"></i> Patients
            </a>
            <a href="health-centers.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'health-centers.php' ? 'active' : ''; ?>">
                <i class="fas fa-hospital"></i> Centers
            </a>
        </nav>
        <div class="user-menu">
            <span><i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
            <a href="logout.php" class="btn btn-sm btn-danger">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>
</header>
