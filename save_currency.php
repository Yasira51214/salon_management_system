<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Escape user inputs for security
    $currency = mysqli_real_escape_string($conn, $_POST['currency']);
    
    // Check if there's an existing currency
    $sqlCheck = "SELECT COUNT(*) AS count FROM currency";
    $resultCheck = mysqli_query($conn, $sqlCheck);
    
    if (!$resultCheck) {
        die("SQL error: " . mysqli_error($conn));
    }
    
    $row = mysqli_fetch_assoc($resultCheck);
    $exists = $row['count'] > 0;
    
    if ($exists) {
        // Update existing currency
        $sql = "UPDATE currency SET ss_currency='$currency' WHERE ss_no = 1";
    } else {
        // Insert new currency
        $sql = "INSERT INTO currency (ss_no, ss_currency) VALUES (1, '$currency')";
    }

    // Perform query
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Setting saved successfully');</script>";
        echo "<script>window.location.href = 'currency_setting.php';</script>";
    } else {
        echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
        echo "<script>window.location.href = 'currency_setting.php';</script>";
    }

    // Close connection
    mysqli_close($conn);
}
?>
