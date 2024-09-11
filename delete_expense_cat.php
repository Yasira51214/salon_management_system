<?php
include 'db_connection.php';

// Establish the database connection
$conn = openConnection();

// Check if ID is set
if (isset($_POST['id'])) {
    $id = $_POST['id'];

    // SQL query to delete the expense category
    $sql = "DELETE FROM expense_cat WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => $stmt->error]);
        }

        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'ID not set']);
}

// Close the database connection
closeConnection($conn);
?>
