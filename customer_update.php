<?php
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $c_no = $_POST['c_no'];
    $c_name = $_POST['c_name'];
    $c_mobile = $_POST['c_mobile'];
    $c_birthday = $_POST['c_birthday'];
    $c_note = $_POST['c_note'];
    $c_cat = $_POST['c_cat'];

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare SQL statement
    $sql = "UPDATE customer SET c_name=?, c_mobile=?, c_birthday=?, c_note=?, c_cat=? WHERE c_no=?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }

    // Bind parameters and execute SQL query
    $stmt->bind_param('sssssi', $c_name, $c_mobile, $c_birthday, $c_note, $c_cat, $c_no);
    if ($stmt->execute()) {
        echo "<script>
                alert('Customer updated successfully.');
                window.location.href = 'customer_list.php';
              </script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
