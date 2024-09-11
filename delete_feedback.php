<?php
// Start the session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Database connection
include 'db_connection.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the delete request is valid
if (isset($_POST['action']) && $_POST['action'] === 'delete' && isset($_POST['date']) && isset($_POST['customerName'])) {
    $date = $_POST['date'];
    $customerName = $_POST['customerName'];

    // Prepare and execute the delete statement
    $sql = "DELETE f FROM feedback f
            JOIN customer c ON f.f_c_no = c.c_no
            WHERE f.f_date = ? AND c.c_name = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }
    $stmt->bind_param('ss', $date, $customerName);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "<p>Feedback successfully deleted.</p>";
    } else {
        echo "<p>Failed to delete feedback or no feedback found.</p>";
    }

    $stmt->close();
}

$conn->close();

// Redirect back to feedback main page
header("Location: feedback_main.php");
exit();
