<?php
include 'db_connection.php';

// Check if the 'id' parameter is set in the URL
if (isset($_GET['id'])) {
    $expense_id = $_GET['id'];

    // Prepare the SQL query to delete the expense record
    $sql = "DELETE FROM `expense` WHERE `e_no` = ?";

    // Prepare and bind the statement
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $expense_id);

        // Execute the query
        if ($stmt->execute()) {
            // Redirect back to the balance view page
            header("Location: balance_sheet.php?date=" . $_GET['date']);
            exit();
        } else {
            echo "Error executing query: " . $stmt->error;
        }

        // Close the statement
        $stmt->close();
    } else {
        echo "Error preparing query: " . $conn->error;
    }
} else {
    echo "Invalid request.";
}

// Close the database connection
$conn->close();
?>
