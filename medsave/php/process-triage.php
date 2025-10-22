<?php
require_once 'config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Get form data
$name = mysqli_real_escape_string($conn, $_POST['name']);
$age = intval($_POST['age']);
$gestational_age = intval($_POST['gestational_age']);
$blood_pressure = mysqli_real_escape_string($conn, $_POST['blood_pressure']);
$temperature = floatval($_POST['temperature']);
$heart_rate = intval($_POST['heart_rate']);
$symptoms = mysqli_real_escape_string($conn, $_POST['symptoms']);
$medical_history = mysqli_real_escape_string($conn, $_POST['medical_history']);
$current_medication = mysqli_real_escape_string($conn, $_POST['current_medication']);
$allergies = mysqli_real_escape_string($conn, $_POST['allergies']);
$previous_pregnancies = mysqli_real_escape_string($conn, $_POST['previous_pregnancies']);
$complications = mysqli_real_escape_string($conn, $_POST['complications']);

// Calculate risk score
$risk_score = 0;
$risk_factors = [];

// Blood pressure assessment
if (preg_match('/(\d+)\/(\d+)/', $blood_pressure, $matches)) {
    $systolic = intval($matches[1]);
    $diastolic = intval($matches[2]);
    
    if ($systolic >= 140 || $diastolic >= 90) {
        $risk_score += 30;
        $risk_factors[] = "Hypertension detected (BP: $blood_pressure)";
    }
    if ($systolic >= 160 || $diastolic >= 110) {
        $risk_score += 20;
        $risk_factors[] = "Severe hypertension - preeclampsia risk";
    }
}

// Temperature assessment
if ($temperature >= 38) {
    $risk_score += 25;
    $risk_factors[] = "Fever present ($temperature°C) - possible infection";
}

// Heart rate assessment
if ($heart_rate > 100 || $heart_rate < 60) {
    $risk_score += 15;
    $risk_factors[] = "Abnormal heart rate ($heart_rate bpm)";
}

// Gestational age assessment
if ($gestational_age < 37 && stripos($symptoms, 'contraction') !== false) {
    $risk_score += 35;
    $risk_factors[] = "Preterm labor risk (Week $gestational_age)";
}
if ($gestational_age > 41) {
    $risk_score += 20;
    $risk_factors[] = "Post-term pregnancy (Week $gestational_age)";
}

// Symptoms assessment
$high_risk_symptoms = ['bleeding', 'severe headache', 'vision', 'chest pain', 'seizure', 'unconscious', 'severe abdominal pain', 'convulsion'];
$medium_risk_symptoms = ['swelling', 'decreased movement', 'contractions', 'nausea', 'vomiting', 'dizziness'];

$symptoms_lower = strtolower($symptoms);
foreach ($high_risk_symptoms as $symptom) {
    if (stripos($symptoms_lower, $symptom) !== false) {
        $risk_score += 30;
        $risk_factors[] = "High-risk symptom: $symptom";
    }
}

foreach ($medium_risk_symptoms as $symptom) {
    if (stripos($symptoms_lower, $symptom) !== false) {
        $risk_score += 10;
        $risk_factors[] = "Concerning symptom: $symptom";
    }
}

// Medical history assessment
$history_lower = strtolower($medical_history);
if (stripos($history_lower, 'diabetes') !== false || stripos($history_lower, 'hypertension') !== false || stripos($history_lower, 'heart') !== false) {
    $risk_score += 20;
    $risk_factors[] = "Pre-existing medical condition";
}

if (stripos(strtolower($complications), 'previous') !== false || stripos(strtolower($complications), 'c-section') !== false) {
    $risk_score += 15;
    $risk_factors[] = "Previous complications noted";
}

// Determine urgency
if ($risk_score >= 60) {
    $urgency_level = 'EMERGENCY';
    $action_required = 'Immediate transfer required';
    $timeframe = 'Within 30 minutes';
} elseif ($risk_score >= 30) {
    $urgency_level = 'MEDIUM';
    $action_required = 'Urgent assessment needed';
    $timeframe = 'Within 2 hours';
} else {
    $urgency_level = 'LOW';
    $action_required = 'Routine care appropriate';
    $timeframe = 'Regular monitoring';
}

// Recommend health center
if ($urgency_level == 'EMERGENCY') {
    $center_query = "SELECT * FROM health_centers WHERE level = 'Tertiary' ORDER BY RAND() LIMIT 1";
} elseif ($urgency_level == 'MEDIUM') {
    $center_query = "SELECT * FROM health_centers WHERE level = 'Secondary' ORDER BY RAND() LIMIT 1";
} else {
    $center_query = "SELECT * FROM health_centers WHERE level = 'Primary' ORDER BY RAND() LIMIT 1";
}

$center_result = mysqli_query($conn, $center_query);
$recommended_center = mysqli_fetch_assoc($center_result);

// Get AI clinical support
$ai_support = getAIClinicalSupport([
    'name' => $name,
    'age' => $age,
    'gestational_age' => $gestational_age,
    'blood_pressure' => $blood_pressure,
    'temperature' => $temperature,
    'heart_rate' => $heart_rate,
    'symptoms' => $symptoms,
    'medical_history' => $medical_history,
    'complications' => $complications
], $risk_factors, $urgency_level);

// Insert patient record
$risk_factors_json = json_encode($risk_factors);
$insert_query = "INSERT INTO patients (
    name, age, gestational_age, blood_pressure, temperature, heart_rate,
    symptoms, medical_history, current_medication, allergies, previous_pregnancies,
    complications, risk_score, urgency_level, risk_factors, recommended_center_id,
    ai_clinical_support, registered_by
) VALUES (
    '$name', $age, $gestational_age, '$blood_pressure', $temperature, $heart_rate,
    '$symptoms', '$medical_history', '$current_medication', '$allergies', '$previous_pregnancies',
    '$complications', $risk_score, '$urgency_level', '$risk_factors_json', {$recommended_center['id']},
    '" . mysqli_real_escape_string($conn, $ai_support) . "', {$_SESSION['user_id']}
)";

if (mysqli_query($conn, $insert_query)) {
    $patient_id = mysqli_insert_id($conn);
    
    // Send WhatsApp alert for emergency cases
    $whatsapp_sent = false;
    if ($urgency_level == 'EMERGENCY') {
        $whatsapp_sent = sendWhatsAppAlert($patient_id, $name, $age, $gestational_age, $blood_pressure, 
                                          $temperature, $heart_rate, $urgency_level, $recommended_center, $risk_factors);
        
        mysqli_query($conn, "UPDATE patients SET whatsapp_alert_sent = 1 WHERE id = $patient_id");
    }
    
    // Create referral record
    mysqli_query($conn, "INSERT INTO referrals (patient_id, to_center_id, urgency_level, notes) 
                        VALUES ($patient_id, {$recommended_center['id']}, '$urgency_level', 'AI-generated referral')");
    
    echo json_encode([
        'success' => true,
        'patient_id' => $patient_id,
        'risk_score' => $risk_score,
        'urgency_level' => $urgency_level,
        'action_required' => $action_required,
        'timeframe' => $timeframe,
        'risk_factors' => $risk_factors,
        'recommended_center' => $recommended_center,
        'ai_support' => $ai_support,
        'whatsapp_sent' => $whatsapp_sent
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_error($conn)]);
}

function getAIClinicalSupport($patient, $risk_factors, $urgency) {
    $prompt = "You are an experienced obstetrician providing clinical decision support. Analyze this maternal case:

Patient Information:
- Age: {$patient['age']} years
- Gestational Age: {$patient['gestational_age']} weeks
- Vital Signs: BP {$patient['blood_pressure']}, Temp {$patient['temperature']}°C, HR {$patient['heart_rate']} bpm
- Symptoms: {$patient['symptoms']}
- Medical History: {$patient['medical_history']}
- Complications: {$patient['complications']}

Risk Assessment:
- Urgency Level: $urgency
- Risk Factors: " . implode('; ', $risk_factors) . "

Provide:
1. Immediate clinical recommendations (2-3 key actions)
2. Potential differential diagnoses
3. Recommended investigations/tests
4. Warning signs to monitor
5. Management priorities

Be concise but comprehensive.";

    // Note: In production, replace with actual Claude API call
    // For now, return a structured response
    return "CLINICAL DECISION SUPPORT:

1. IMMEDIATE RECOMMENDATIONS:
   - Continuous vital signs monitoring
   - Fetal heart rate monitoring
   - IV access and hydration
   
2. DIFFERENTIAL DIAGNOSES:
   - Consider based on risk factors identified
   - Rule out preeclampsia/eclampsia if hypertensive
   - Assess for preterm labor if applicable
   
3. RECOMMENDED TESTS:
   - Complete blood count
   - Urinalysis with protein
   - Ultrasound assessment
   - Non-stress test (NST)
   
4. WARNING SIGNS:
   - Worsening headache
   - Visual disturbances
   - Decreased fetal movement
   - Vaginal bleeding
   - Severe abdominal pain
   
5. MANAGEMENT PRIORITIES:
   - Stabilize patient
   - Ensure timely referral
   - Continuous monitoring
   - Prepare for emergency intervention if needed";
}

function sendWhatsAppAlert($patient_id, $name, $age, $gestational_age, $bp, $temp, $hr, $urgency, $center, $risk_factors) {
    // In production, integrate with WhatsApp Business API
    // For demo purposes, log the message
    $message = "🚨 URGENT MATERNAL REFERRAL ALERT\n\n";
    $message .= "Patient: $name\n";
    $message .= "Age: $age years\n";
    $message .= "Gestational Age: $gestational_age weeks\n";
    $message .= "Urgency: $urgency\n";
    $message .= "Risk Factors: " . implode(', ', array_slice($risk_factors, 0, 3)) . "\n\n";
    $message .= "Vital Signs:\n";
    $message .= "- BP: $bp\n";
    $message .= "- Temp: {$temp}°C\n";
    $message .= "- HR: $hr bpm\n\n";
    $message .= "Referral Center: {$center['name']}\n";
    $message .= "Contact: {$center['contact']}";
    
    // Log for demo
    error_log("WhatsApp Alert: " . $message);
    
    return true; // Return true to indicate successful send
}
?>

