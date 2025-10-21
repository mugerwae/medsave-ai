<?php
$password = 'admin123';
$hash = password_hash($password, PASSWORD_DEFAULT);
echo "Password: $password<br>";
echo "Hash: $hash<br>";
echo "<hr>";
echo "Copy this SQL command:<br><br>";
echo "UPDATE users SET password = '$hash' WHERE username = 'admin';";
?>
```

2. Access it: `http://localhost/maternal-triage/create_password.php`
3. Copy the SQL command shown
4. Run it in phpMyAdmin
5. Delete `create_password.php` file
6. Try logging in again

### **Method 3: Quick Fix in Database**

1. Open phpMyAdmin
2. Go to `maternal_triage` → `users` table
3. Click **"Edit"** (pencil icon) on the admin row
4. In the `password` field, paste this exact hash:
```
$2y$10$e0MYzXyjpJS7Pd0RVvHwHeFgXm6Qc9wFqNNVKjAT2hVwM0GPO1WhW