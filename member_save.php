<?php
include 'db_connection.php';

function encryptPassword($password, $key) {
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
    $encryptedPassword = openssl_encrypt($password, 'aes-256-cbc', $key, 0, $iv);
    return base64_encode($encryptedPassword . '::' . $iv); // Concatenate with the IV and encode
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullName = $_POST['fullName'];
    $userName = $_POST['userName'];
    $password = $_POST['password'];
    $role = $_POST['role'];
    $email = $_POST['email'];

    // Generate or retrieve your encryption key
    $key = 'your-encryption-key-here'; // Replace with your actual key, ensure it is securely stored

    // Encrypt the password before storing it
    $encryptedPassword = encryptPassword($password, $key);

    // Determine the role number
    $roleNumber = ($role === "Admin") ? 0 : 1;

    // Check if the email or username already exists
    $checkStmt = $conn->prepare("SELECT m_mail, m_name FROM member WHERE m_mail = ? OR m_name = ?");
    if ($checkStmt === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }

    $checkBind = $checkStmt->bind_param("ss", $email, $userName);
    if ($checkBind === false) {
        die('Bind failed: ' . htmlspecialchars($checkStmt->error));
    }

    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        echo "<script>
                alert('Email or Username already registered.');
                window.location.href = 'member_add.php';
              </script>";
        $checkStmt->close();
        $conn->close();
        exit();
    }

    $checkStmt->close();

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO member (m_fullname, m_name, m_password, m_role, m_mail) VALUES (?, ?, ?, ?, ?)");
    if ($stmt === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }

    $bind = $stmt->bind_param("sssis", $fullName, $userName, $encryptedPassword, $roleNumber, $email);
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
        header("Location: member_list.php?success=1");
        exit();
    } else {
        echo "<h1>Failed to Add Member: " . htmlspecialchars($stmt->error) . "</h1>";
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>
