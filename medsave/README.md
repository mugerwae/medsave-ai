# Maternal AI Triage System

### System Requirements
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache Web Server
- Modern web browser

### Installation Instructions

1. **Extract Files**
   - Extract all files to your web server directory (htdocs/www/public_html)

2. **Create Database**
   - Open phpMyAdmin or MySQL command line
   - Create a new database: `u559191231_medsavedb`
   - Import the `u559191231_medsavedb.sql` file

3. **Configure Database Connection**
   - Open `config.php`
   - Update the following constants:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_USER', 'your_username');
     define('DB_PASS', 'your_password');
     define('DB_NAME', 'u559191231_medsavedb');
     ```

4. **Set Permissions**
   - Ensure web server has read/write permissions on the directory

5. **Access Application**
   - Open browser: `http://localhost/medsave/`
   - Default login: `admin` / `admin123`

### Features

#### Core Features
- ✅ Intelligent Risk Assessment Algorithm
- ✅ AI Clinical Decision Support (Claude API integration ready)
- ✅ Smart Health Center Referral System
- ✅ Emergency WhatsApp Alerts (API integration ready)
- ✅ AI Learning Loop with Feedback System
- ✅ Comprehensive Patient History Management
- ✅ Real-time Dashboard with Statistics
- ✅ Search and Filter Functionality

#### Risk Assessment Factors
- Blood pressure monitoring (Hypertension/Preeclampsia detection)
- Temperature assessment (Infection detection)
- Heart rate monitoring
- Gestational age evaluation
- Symptom analysis (High-risk and medium-risk symptoms)
- Medical history consideration
- Previous complications tracking

#### Urgency Levels
- **EMERGENCY** (Score ≥60): Immediate transfer required (Within 30 minutes)
- **MEDIUM** (Score 30-59): Urgent assessment needed (Within 2 hours)
- **LOW** (Score <30): Routine care appropriate (Regular monitoring)

### File Structure
```
maternal-triage/
├── config.php              # Database configuration
├── index.php              # Dashboard
├── login.php              # Login page
├── logout.php             # Logout handler
├── new-patient.php        # Patient registration form
├── process-triage.php     # Triage processing logic
├── view-patient.php       # Patient details view
├── patients.php           # All patients list
├── health-centers.php     # Health centers directory
├── database.sql           # Database schema
├── README.md              # This file
├── api/
│   ├── submit-feedback.php  # Feedback submission
│   └── send-alert.php       # WhatsApp alert handler
├── includes/
│   ├── header.php          # Header template
│   └── footer.php          # Footer template
└── assets/
    ├── css/
    │   └── style.css       # Main stylesheet
    └── js/
        └── patient-form.js  # Form handling script
```

### API Integration

#### Claude AI Integration
To enable AI clinical support:
1. Get API key from Anthropic
2. Update in `config.php`:
   ```php
   define('ANTHROPIC_API_KEY', 'your-api-key-here');
   ```
3. Uncomment API call code in `process-triage.php`

#### WhatsApp Business API Integration
To enable WhatsApp alerts:
1. Set up WhatsApp Business API account
2. Update `sendWhatsAppAlert()` function in `process-triage.php`
3. Add your API credentials

### Security Features
- Password hashing with bcrypt
- SQL injection prevention
- XSS protection
- Session management
- Input validation and sanitization

### User Roles
- **Admin**: Full system access
- **Doctor**: Patient management, triage, feedback
- **Nurse**: Patient registration, basic triage

### Default Users
```
Username: admin
Password: admin123
Role: Administrator
```

### Usage Guide

1. **Login** to the system
2. **Register New Patient** with complete medical history
3. **AI Analysis** automatically calculates risk score
4. **View Results** with clinical recommendations
5. **Referral** to appropriate health center
6. **Emergency Alerts** sent for high-risk cases
7. **Provide Feedback** to improve AI accuracy


### Production Deployment Checklist
- [ ] Change default admin password
- [ ] Update database credentials
- [ ] Add Claude API key
- [ ] Implement WhatsApp Business API
- [ ] Enable HTTPS
- [ ] Set up regular backups
- [ ] Configure error logging
- [ ] Update contact information for health centers

### Support
For support or inquiries, Email: ssenogab999@gmail.com.

### License
Free

### Credits
Developed by Medsave Team - STI Industry 4.o+ Hackthon


