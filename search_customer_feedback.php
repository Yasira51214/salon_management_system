<?php
include 'common.php';

// Get the search parameters from the GET request
$customerName = isset($_GET['customerName']) ? $_GET['customerName'] : '';
$mobile = isset($_GET['mobile']) ? $_GET['mobile'] : '';

// Prepare the SQL query to search for customers
$query = "SELECT * FROM customer WHERE 1=1";
if (!empty($customerName)) {
    $query .= " AND name LIKE '%" . mysqli_real_escape_string($conn, $customerName) . "%'";
}
if (!empty($mobile)) {
    $query .= " AND mobile LIKE '%" . mysqli_real_escape_string($conn, $mobile) . "%'";
}

// Execute the query
$result = mysqli_query($conn, $query);

// Initialize an array to hold the customers
$customers = [];
while ($row = mysqli_fetch_assoc($result)) {
    $customers[] = [
        'name' => $row['name'],
        'mobile' => $row['mobile'],
    ];
}

// Prepare the response
$response = [
    'exists' => !empty($customers),
    'customer' => $customers,
];

// Return the response as JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
