<?php
session_start();

// Include the database connection
require_once 'db.php';

// Check if the data from the login form was submitted
if (!isset($_POST['employee_id'], $_POST['dob'], $_POST['contact_no'], $_POST['otp'])) {
    exit('Please fill all the fields!');
}

// Prepare the SQL statement to prevent SQL injection
if ($stmt = $con->prepare('SELECT `Employee Id`, `DOB`, `Mobile No.`, `OTP` FROM `nfr_accounts` WHERE `Employee Id` = ? AND `DOB` = ? AND `Mobile No.` = ? AND `OTP` = ?')) {
    // Bind parameters (s = string, i = int, etc)
    $stmt->bind_param('ssss', $_POST['employee_id'], $_POST['dob'], $_POST['contact_no'], $_POST['otp']);
    $stmt->execute();
    // Store the result
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Account exists
        $stmt->bind_result($employee_id, $dob, $mobile_no, $otp);
        $stmt->fetch();
        
        // Verification success
        session_regenerate_id();
        $_SESSION['loggedin'] = TRUE;
        $_SESSION['employee_id'] = $employee_id;
        $_SESSION['dob'] = $dob;
        $_SESSION['mobile_no'] = $mobile_no;
        echo 'Welcome, ' . htmlspecialchars($_SESSION['employee_id'], ENT_QUOTES) . '!';
    } else {
        // Incorrect details
        echo 'Incorrect Employee ID, DOB, Mobile No., or OTP!';
    }

    // Close statement
    $stmt->close();
} else {
    // SQL statement preparation failed
    echo 'Could not prepare statement!';
}

// Close connection
$con->close();
?>
