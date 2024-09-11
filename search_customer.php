<?php
include 'common.php';
// Retrieve form data
$name = $_POST['name'];
$mobile = $_POST['mobile'];
$date = $_POST['date'];

// Prepare and execute the query
$sql = "SELECT c_name, c_mobile, c_reg_date FROM customer WHERE c_name LIKE ? OR c_mobile = ? OR c_reg_date = ?";
$stmt = $conn->prepare($sql);
$searchName = "%$name%";
$stmt->bind_param("sss", $searchName, $mobile, $date);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $customer = $result->fetch_assoc();
    echo json_encode($customer);
} else {
    echo json_encode(["c_name" => "Not found", "c_mobile" => "Not found", "c_reg_date" => "Not found"]);
}

$stmt->close();
$conn->close();
?>
