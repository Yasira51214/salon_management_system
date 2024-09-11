<?php
include 'common.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Management</title>
    <link rel="stylesheet" href="./css/customer_view.css">
    <style>
        .details-table {
            width: 100%;
            border-collapse: collapse;
        }

        .details-table td {
            position: relative;
            width: 200px; /* Set a fixed width for table cells */
            height: 40px; /* Set a fixed height for table cells */
            padding: 10px;
            vertical-align: middle; /* Align text in the middle vertically */
            border: 1px solid #ddd; /* Optional: Add a border to cells */
        }

        .c_no {
            color: #ff0000; /* Change to your desired color */
            font-weight: bold;
            position: absolute;
            left: 380px;
            top: 50%;
            transform: translateY(-50%);
        }

        .form-buttons {
            margin-top: 20px;
        }

        .form-buttons button {
            padding: 10px 20px;
            margin: 5px;
        }
    </style>
</head>
<body>
<?php
include 'side_bar.php';

// Get the customer ID from the URL parameter
$c_no = isset($_GET['c_id']) ? $_GET['c_id'] : '';

$index = isset($_GET['index']) ? intval($_GET['index']) : 0;
$c_name = isset($_GET['c_name']) ? mysqli_real_escape_string($conn, $_GET['c_name']) : '';
$c_mobile = isset($_GET['c_mobile']) ? mysqli_real_escape_string($conn, $_GET['c_mobile']) : '';
$selected_date = isset($_GET['selected_date']) ? mysqli_real_escape_string($conn, $_GET['selected_date']) : '';

if (!empty($selected_date)) {
    $sql = "SELECT DISTINCT c.* FROM customer c
            JOIN `order` o ON c.c_no = o.o_c_no
            WHERE o.o_date = '$selected_date'";
} else {
    $sql = "SELECT * FROM customer WHERE 1=1";
    if (!empty($c_name)) {
        $sql .= " AND c_name LIKE '%$c_name%'";
    }
    if (!empty($c_mobile)) {
        $sql .= " AND c_mobile = '$c_mobile'";
    }
}

$result = mysqli_query($conn, $sql);
if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

$customers = [];
while ($row = mysqli_fetch_assoc($result)) {
    $customers[] = $row;
}

// Determine the current customer
if (!empty($c_no)) {
    // Direct view by customer ID
    $customer = array_filter($customers, function($customer) use ($c_no) {
        return $customer['c_no'] == $c_no;
    });
    $customer = reset($customer);
    $index = array_search($customer, $customers);
} else {
    // View based on index
    if (count($customers) > 0) {
        if ($index >= count($customers)) $index = count($customers) - 1;
        if ($index < 0) $index = 0;
        $customer = $customers[$index];
        $c_no = $customer['c_no'];
    } else {
        echo "<script>alert('No data available for the given search criteria.'); window.location.href = 'main.php';</script>";
        exit;
    }
}

// Fetch the last visit date
$last_visit_sql = "SELECT MAX(o_date) AS last_visit FROM `order` WHERE o_c_no = '$c_no'";
$last_visit_result = mysqli_query($conn, $last_visit_sql);
$last_visit_row = mysqli_fetch_assoc($last_visit_result);
$last_visit_date = $last_visit_row['last_visit'];

// Fetch the service history
$service_history_sql = "SELECT o.o_date, s.s_si_no, s.s_qty, s.s_price 
                        FROM `order` o 
                        JOIN `orderservice` s ON o.o_no = s.s_o_no 
                        WHERE o.o_c_no = '$c_no' 
                        ORDER BY o.o_date DESC LIMIT 5";
$service_history_result = mysqli_query($conn, $service_history_sql);
$service_history = [];
while ($service_row = mysqli_fetch_assoc($service_history_result)) {
    $service_history[] = $service_row;
}

mysqli_close($conn);

// Function to format mobile number
function format_mobile_number($mobile) {
    if (strlen($mobile) == 11) {
        return substr($mobile, 0, 4) . '-' . substr($mobile, 4);
    }
    return $mobile;
}
?>
<div class="container">
<h1>Customer View</h1>
<br>
    <div class="search-bar">
        <form action="cust_view_dashboard.php" method="GET">
            <label>Customer Name: <input type="text" name="c_name" value="<?php echo htmlspecialchars($c_name); ?>"></label>
            <label>Mobile Number: <input type="text" name="c_mobile" value="<?php echo htmlspecialchars($c_mobile); ?>"></label>
            <button type="submit">Search</button>
        </form>
    </div>
    <form>
        <input type="hidden" id="currentIndex" value="<?php echo $index; ?>">
        <table class="details-table">
            <tbody>
                <tr>
                    <td>Full Name</td>
                    <td>
                        <?php echo htmlspecialchars($customer['c_name']); ?>
                        <span class="c_no"><?php echo htmlspecialchars($customer['c_no']); ?></span>
                    </td>
                </tr>
                <tr>
                    <td>Mobile No.</td>
                    <td><?php echo htmlspecialchars(format_mobile_number($customer['c_mobile'])); ?></td>
                </tr>
                <tr>
                    <td>Birthday</td>
                    <td><?php echo htmlspecialchars($customer['c_birthday']); ?></td>
                </tr>
                <tr>
                    <td>Note</td>
                    <td><?php echo htmlspecialchars($customer['c_note']); ?></td>
                </tr>
                <tr>
                    <td>Category</td>
                    <td><?php echo htmlspecialchars($customer['c_cat']); ?></td>
                </tr>
                <tr>
                    <td>Registry Date</td>
                    <td><?php echo isset($customer['c_reg_date']) ? htmlspecialchars($customer['c_reg_date']) : 'N/A'; ?></td>
                </tr>
            </tbody>
        </table>
    </form>
    <div class="form-buttons">
        <button id="previousButton">Previous</button>
        <button id="nextButton">Next</button>

        <a href="./main.php" id="cancelLink"><button type="button">Cancel</button></a>
    </div>
</div>

<script>
document.getElementById('previousButton').addEventListener('click', function() {
    let currentIndex = parseInt(document.getElementById('currentIndex').value);
    currentIndex = currentIndex > 0 ? currentIndex - 1 : 0;
    window.location.href = "cust_view_dashboard.php?index=" + currentIndex + 
        "&c_name=<?php echo urlencode($c_name); ?>&c_mobile=<?php echo urlencode($c_mobile); ?>" + 
        "&selected_date=<?php echo urlencode($selected_date); ?>";
});

document.getElementById('nextButton').addEventListener('click', function() {
    let currentIndex = parseInt(document.getElementById('currentIndex').value);
    currentIndex = currentIndex + 1;
    window.location.href = "cust_view_dashboard.php?index=" + currentIndex + 
        "&c_name=<?php echo urlencode($c_name); ?>&c_mobile=<?php echo urlencode($c_mobile); ?>" + 
        "&selected_date=<?php echo urlencode($selected_date); ?>";
});
</script>
</body>
</html>
