<?php
include 'db_connection.php';

// Get the customer ID from the request
$c_no = isset($_GET['c_no']) ? mysqli_real_escape_string($conn, $_GET['c_no']) : '';

if (!empty($c_no)) {
    // Update the customer's status to 'removed' (using integer 1)
    $update_sql = "UPDATE customer SET c_is_del = 1 WHERE c_no = '$c_no'";
    
    if (mysqli_query($conn, $update_sql)) {
        // Redirect back to customer-list.php
        header("Location: customer_list.php");
        exit;
    } else {
        echo "Error updating record: " . mysqli_error($conn);
    }
} else {
    echo "Invalid customer ID.";
}

mysqli_close($conn);
?>
