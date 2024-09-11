<?php
include 'common.php'; // Include your database connection setup
include "side_bar.php";
// Check if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get search criteria from the form if they are set
$nameSearch = isset($_POST['nameSearch']) ? $conn->real_escape_string($_POST['nameSearch']) : '';
$numberSearch = isset($_POST['numberSearch']) ? $conn->real_escape_string($_POST['numberSearch']) : '';
$dateSearch = isset($_POST['dateSearch']) ? $conn->real_escape_string($_POST['dateSearch']) : '';

// Pagination setup
$limit = 10; // Number of records per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Define the query to count total records for pagination
$countQuery = "
    SELECT COUNT(DISTINCT o.o_no) as total
    FROM `order` o
    JOIN orderservice os ON o.o_no = os.s_o_no
    JOIN service_item si ON os.s_si_no = si.si_no
    JOIN customer c ON o.o_c_no = c.c_no
    WHERE 1=1
";

// Append filters to the count query
if (!empty($nameSearch)) {
    $countQuery .= " AND c.c_name LIKE '%$nameSearch%'";
}
if (!empty($numberSearch)) {
    $countQuery .= " AND c.c_mobile LIKE '%$numberSearch%'";
}
if (!empty($dateSearch)) {
    $countQuery .= " AND o.o_date = '$dateSearch'";
}

$countResult = $conn->query($countQuery);
$totalRecords = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalRecords / $limit);

// Define the query with optional filters and pagination
$query = "
    SELECT 
        o.o_no, 
        o.o_c_no,
        o.o_date, 
        o.o_amount, 
        si.si_service_name,
        c.c_name,
        c.c_mobile
    FROM `order` o
    JOIN orderservice os ON o.o_no = os.s_o_no
    JOIN service_item si ON os.s_si_no = si.si_no
    JOIN customer c ON o.o_c_no = c.c_no
    WHERE 1=1
";

// Append filters to the query
if (!empty($nameSearch)) {
    $query .= " AND c.c_name LIKE '%$nameSearch%'";
}
if (!empty($numberSearch)) {
    $query .= " AND c.c_mobile LIKE '%$numberSearch%'";
}
if (!empty($dateSearch)) {
    $query .= " AND o.o_date = '$dateSearch'";
}

$query .= " GROUP BY o.o_no LIMIT $limit OFFSET $offset";

// Execute the query
$result = $conn->query($query);

// Check for query errors
if ($result === false) {
    die("Query failed: " . $conn->error);
}

// Initialize an empty array to hold the booking data
$bookings = [];

// Fetch the results if the query was successful
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $o_no = $row['o_no'];
        // Create a unique key for each order
        if (!isset($bookings[$o_no])) {
            $bookings[$o_no] = [
                'o_date' => $row['o_date'],
                'o_amount' => $row['o_amount'],
                'c_name' => $row['c_name'],
                // 'c_mobile' => $row['c_mobile'],
                'services' => []
            ];
        }
        // Append the service name to the order's services
        $bookings[$o_no]['services'][] = $row['si_service_name'];
    }
}

// Convert the associative array to a numerically indexed array for easier use in the template
$bookings = array_values($bookings);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking History</title>

    <link rel="stylesheet" href="./css/booking_history.css">
    <style>
    /* Style the date input field */
    input[type="date"] {
        padding: 8px;
            margin-right: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        width: 200px;
    }
   
    
</style>
</head>
<body>
    <div class="container">
        <h1 style="color: #cd015b;">Booking History</h1>

        <!-- Search Bar -->
        <form method="POST" action="">
            <div class="search-bar">
                <input type="text" name="nameSearch" placeholder="Search by Name" value="<?php echo htmlspecialchars($nameSearch); ?>">
                <input type="text" name="numberSearch" placeholder="Search by Number" value="<?php echo htmlspecialchars($numberSearch); ?>">
                <input type="date" name="dateSearch" value="<?php echo htmlspecialchars($dateSearch); ?>">
                <button type="submit" id="searchBtn">Search</button>
            </div>
        </form>

        <!-- Booking Table -->
        <table class="customer-table"> <!-- Apply the table class -->
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Customer Name</th>
                    <th>Service</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bookings as $index => $booking): ?>
                    <tr>
                        <td><?php echo $index + 1 + $offset; ?></td>
                        <td>
                            <?php 
                            $o_no = isset($booking['o_no']) ? htmlspecialchars($booking['o_no']) : $o_no;
                            $o_date = isset($booking['o_date']) ? htmlspecialchars($booking['o_date']) : $o_date;
                            ?>
                            <a href="booking_view.php?o_no=<?php echo urlencode($o_no); ?>"><?php echo $o_date;?></a>
                        </td>
                        <!-- Display the customer name -->
                        <td><?php echo isset($booking['c_name']) ? htmlspecialchars($booking['c_name']) : $c_name; ?></td>
                        <td><?php echo nl2br(htmlspecialchars(implode("\n", $booking['services']))); ?></td>
                        <td><?php echo isset($booking['o_amount']) ? htmlspecialchars($booking['o_amount']) : $c_name; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="pagination">
            <?php if($page > 1): ?>
                <button class="page-btn" onclick="window.location.href='?page=<?php echo $page - 1; ?>'">« Previous</button>
            <?php endif; ?>

            <?php for($i = 1; $i <= $totalPages; $i++): ?>
                <button class="page-btn <?php if($i == $page) echo 'active'; ?>" onclick="window.location.href='?page=<?php echo $i; ?>'"><?php echo $i; ?></button>
            <?php endfor; ?>

            <?php if($page < $totalPages): ?>
                <button class="page-btn" onclick="window.location.href='?page=<?php echo $page + 1; ?>'">Next Page »</button>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
