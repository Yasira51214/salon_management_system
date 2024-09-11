<?php
include 'db_connection.php';

if (isset($_GET['user'])) {
    $userName = $_GET['user'];


    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare and bind
    $stmt = $conn->prepare("DELETE FROM member WHERE m_name = ?");
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }

    $bind = $stmt->bind_param("s", $userName);
    if ($bind === false) {
        die("Error binding parameters: " . $stmt->error);
    }

    // Execute the statement
    $exec = $stmt->execute();
    if ($exec) {
        echo "<script>alert('Member deleted successfully.'); window.location.href='member_list.php';</script>";
    } else {
        echo "<script>alert('Failed to delete member: " . htmlspecialchars($stmt->error) . "'); window.location.href='member_list.php';</script>";
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
} else {
    echo "<script>alert('Invalid request.'); window.location.href='member_list.php';</script>";
}
?>
