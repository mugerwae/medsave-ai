document.getElementById('patientForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const modal = document.getElementById('loadingModal');
    modal.classList.add('active');
    
    fetch('process-triage.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        modal.classList.remove('active');
        
        if (data.success) {
            window.location.href = 'view-patient.php?id=' + data.patient_id;
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        modal.classList.remove('active');
        alert('An error occurred. Please try again.');
        console.error('Error:', error);
    });
});

// Blood pressure validation
document.getElementById('blood_pressure').addEventListener('blur', function() {
    const value = this.value;
    const pattern = /^\d{2,3}\/\d{2,3}$/;
    
    if (value && !pattern.test(value)) {
        alert('Please enter blood pressure in format: 120/80');
        this.focus();
    }
});
