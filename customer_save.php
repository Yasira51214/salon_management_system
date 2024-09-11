<?php
include 'db_connection.php';

// Fetch parameters from the POST request
$customerName = $_POST['c_name'];
$customerMobile = $_POST['c_mobile']; // This will be in the format 0300-0000000
$customerBirthday = $_POST['c_birthday'];
$customerNote = $_POST['c_note'];
$customerCategory = $_POST['c_cat'];
$currentDate = date('Y-m-d');
$action = $_POST['action'];

// Validate Full Name
if (!preg_match('/^[A-Za-z\s]{1,30}$/', $customerName)) {
    header("Location: register.php?error=invalid_name");
    exit();
}

// Clean the mobile number by removing the hyphen
$cleanedMobile = str_replace('-', '', $customerMobile);

// Validate the cleaned mobile number
if (!preg_match('/^\d{11}$/', $cleanedMobile)) {
    header("Location: register.php?error=invalid_mobile");
    exit();
}

// Check if the customer name and mobile number combination already exists
$check_sql = "SELECT c_name, c_mobile FROM customer WHERE c_name = ? AND c_mobile = ?";
$stmt = $conn->prepare($check_sql);
$stmt->bind_param('ss', $customerName, $cleanedMobile);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    // Customer name and mobile number combination already exists, redirect back with an error message
    header("Location: register.php?error=duplicate_customer");
    $stmt->close();
    $conn->close();
    exit();
}

$stmt->close();

// Proceed with saving the new customer data
$save_sql = "INSERT INTO customer (c_name, c_mobile, c_birthday, c_note, c_cat, c_reg_date) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($save_sql);
$stmt->bind_param('ssssss', $customerName, $cleanedMobile, $customerBirthday, $customerNote, $customerCategory, $currentDate);

if ($stmt->execute()) {
    if ($action == 'save_new') {
        header("Location: register.php?saved=true");
    } else {
        header("Location: customer_list.php?saved=true");
    }
} else {
    echo "Error saving customer data: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
