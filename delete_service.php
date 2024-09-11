<?php
include 'db_connection.php';

// Check if 'id' is set in the URL
if (isset($_GET['id'])) {
    $serviceId = $_GET['id'];

    // Create SQL delete statement
    $sql = "DELETE FROM service_item WHERE si_no = ?";

    // Prepare and bind
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $serviceId);

        // Execute the statement
        if ($stmt->execute()) {
            echo "Record deleted successfully";
        } else {
            echo "Error deleting record: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
} else {
    echo "No ID provided.";
}

$conn->close();

// Redirect back to the main page
header("Location:./service_list.php");
exit();
?>
