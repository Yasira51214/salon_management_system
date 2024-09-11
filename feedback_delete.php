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

// Get parameters from query string
$customerName = isset($_GET['customerName']) ? $_GET['customerName'] : '';
$selectedDate = isset($_GET['date']) ? $_GET['date'] : '';

if (!empty($customerName) && !empty($selectedDate)) {
    // Fetch customer number (c_no) based on customer name
    $stmt = $conn->prepare("SELECT c_no FROM customer WHERE c_name = ?");
    $stmt->bind_param('s', $customerName);
    $stmt->execute();
    $stmt->bind_result($customerNo);
    $stmt->fetch();
    $stmt->close();

    if ($customerNo) {
        // Delete feedback based on customer number and date
        $stmt = $conn->prepare("DELETE FROM feedback WHERE f_c_no = ? AND f_date = ?");
        $stmt->bind_param('is', $customerNo, $selectedDate);
        $stmt->execute();
        $stmt->close();

        // Redirect back to the main feedback page
        header("Location: feedback_main.php");
        exit();
    } else {
        echo "Customer not found.";
    }
} else {
    echo "Invalid parameters.";
}

$conn->close();
?>
<script>
function deleteFeedback(customerName, selectedDate) {
    if (confirm('Are you sure you want to delete this feedback?')) {
        window.location.href = 'feedback_delete.php?customerName=' + encodeURIComponent(customerName) + '&date=' + encodeURIComponent(selectedDate);
    }
}
</script>
