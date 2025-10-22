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
    <title>New Patient Registration - Maternal AI Triage</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container">
        <div class="page-header">
            <h1><i class="fas fa-user-plus"></i> Patient Registration & Triage</h1>
            <p>Complete all required fields for accurate AI assessment</p>
        </div>

        <form id="patientForm" class="patient-form">
            <div class="form-section">
                <h2><i class="fas fa-user"></i> Patient Information</h2>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="name">Patient Name <span class="required">*</span></label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="age">Age <span class="required">*</span></label>
                        <input type="number" id="age" name="age" min="10" max="60" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="gestational_age">Gestational Age (weeks) <span class="required">*</span></label>
                        <input type="number" id="gestational_age" name="gestational_age" min="1" max="45" required>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h2><i class="fas fa-heartbeat"></i> Vital Signs</h2>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="blood_pressure">Blood Pressure <span class="required">*</span></label>
                        <input type="text" id="blood_pressure" name="blood_pressure" placeholder="e.g., 120/80" required>
                        <small>Format: Systolic/Diastolic</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="temperature">Temperature (°C) <span class="required">*</span></label>
                        <input type="number" id="temperature" name="temperature" step="0.1" min="35" max="42" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="heart_rate">Heart Rate (bpm) <span class="required">*</span></label>
                        <input type="number" id="heart_rate" name="heart_rate" min="40" max="200" required>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h2><i class="fas fa-notes-medical"></i> Clinical Information</h2>
                <div class="form-group">
                    <label for="symptoms">Current Symptoms <span class="required">*</span></label>
                    <textarea id="symptoms" name="symptoms" rows="4" required placeholder="Describe all current symptoms (e.g., headache, bleeding, contractions, swelling, vision changes...)"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="medical_history">Medical History</label>
                    <textarea id="medical_history" name="medical_history" rows="3" placeholder="Pre-existing conditions (diabetes, hypertension, heart disease, etc.)"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="current_medication">Current Medication</label>
                    <textarea id="current_medication" name="current_medication" rows="2" placeholder="List all current medications"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="allergies">Allergies</label>
                    <textarea id="allergies" name="allergies" rows="2" placeholder="List any known allergies"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="previous_pregnancies">Previous Pregnancies</label>
                    <textarea id="previous_pregnancies" name="previous_pregnancies" rows="2" placeholder="Number of previous pregnancies, outcomes, etc."></textarea>
                </div>
                
                <div class="form-group">
                    <label for="complications">Previous Complications</label>
                    <textarea id="complications" name="complications" rows="3" placeholder="Any previous complications, C-sections, miscarriages, etc."></textarea>
                </div>
            </div>

            <div class="form-actions">
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <i class="fas fa-brain"></i> Analyze & Generate Triage
                </button>
            </div>
        </form>

        <div id="loadingModal" class="modal">
            <div class="modal-content">
                <div class="loader"></div>
                <p>Analyzing patient data with AI...</p>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    <script src="assets/js/patient-form.js"></script>
</body>
</html>

