<?php
include 'db_connection.php';

function encryptPassword($password, $key) {
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
    $encryptedPassword = openssl_encrypt($password, 'aes-256-cbc', $key, 0, $iv);
    return base64_encode($encryptedPassword . '::' . $iv);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input
    $fullName = htmlspecialchars($_POST['fullName']);
    $userName = htmlspecialchars($_POST['userName']);
    $password = htmlspecialchars($_POST['password']);
    $role = htmlspecialchars($_POST['role']);
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);

    if (!$email) {
        die("Invalid email format");
    }

    // Determine the role number
    $roleNumber = ($role === "Admin") ? 0 : 1;

    // Generate or retrieve your encryption key
    $key = 'your-encryption-key-here'; // Replace with your actual key, ensure it is securely stored

    // Encrypt the password
    $encryptedPassword = encryptPassword($password, $key);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare and bind
    $stmt = $conn->prepare("UPDATE member SET m_fullname = ?, m_password = ?, m_role = ?, m_mail = ? WHERE m_name = ?");
    if ($stmt === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }

    $bind = $stmt->bind_param("ssiss", $fullName, $encryptedPassword, $roleNumber, $email, $userName);
    if ($bind === false) {
        die('Bind failed: ' . htmlspecialchars($stmt->error));
    }

    // Execute the statement
    $exec = $stmt->execute();
    if ($exec) {
        // Close the statement and connection
        $stmt->close();
        $conn->close();

        // Redirect to member list with success message
        header("Location: member_list.php?update_success=1");
        exit();
    } else {
        echo "<h1>Failed to Update Member: " . htmlspecialchars($stmt->error) . "</h1>";
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>
