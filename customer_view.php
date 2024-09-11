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
        .details-table td {
            position: relative;
        }
        /* c_no number style */
        .c_no {
            color: #ff0000; /
            font-weight: bold;
            position: absolute;
            right: 25px;
            top: 50%;
            width: 35px;
            height: 25px;
            text-align: center;
            transform: translateY(-50%);
            border: 1px solid red;

        }
    </style>
</head>
<body>
<?php
include 'side_bar.php';

// Get parameters
$c_id = isset($_GET['c_id']) ? mysqli_real_escape_string($conn, $_GET['c_id']) : '';
$index = isset($_GET['index']) ? intval($_GET['index']) : 0;
$c_name = isset($_GET['c_name']) ? mysqli_real_escape_string($conn, $_GET['c_name']) : '';
$c_mobile = isset($_GET['c_mobile']) ? mysqli_real_escape_string($conn, $_GET['c_mobile']) : '';

// Fetch all matching customers if needed
$sql = "SELECT * FROM customer WHERE 1=1";
if (!empty($c_name)) {
    $sql .= " AND c_name LIKE '%$c_name%'";
}
if (!empty($c_mobile)) {
    $sql .= " AND c_mobile = '$c_mobile'";
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
if (!empty($c_id)) {
    // Direct view by customer ID
    $customer = array_filter($customers, function($customer) use ($c_id) {
        return $customer['c_no'] == $c_id;
    });
    $customer = reset($customer);
    $index = array_search($customer, $customers);
} else {
    // View based on index
    if (count($customers) > 0) {
        if ($index >= count($customers)) $index = count($customers) - 1;
        if ($index < 0) $index = 0;
        $customer = $customers[$index];
        $c_id = $customer['c_no'];
    } else {
        echo "<script>alert('No data available for the given search criteria.'); window.location.href = 'customer-list.php';</script>";
        exit;
    }
}

// Fetch the last visit date
$last_visit_sql = "SELECT MAX(o_date) AS last_visit FROM `order` WHERE o_c_no = '$c_id'";
$last_visit_result = mysqli_query($conn, $last_visit_sql);
$last_visit_row = mysqli_fetch_assoc($last_visit_result);
$last_visit_date = $last_visit_row['last_visit'];

// Fetch the service history
$service_history_sql = "SELECT o.o_date, s.s_si_no, s.s_qty, s.s_price 
                        FROM `order` o 
                        JOIN `orderservice` s ON o.o_no = s.s_o_no 
                        WHERE o.o_c_no = '$c_id' 
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
    <form action="customer_list.php" method="GET" onsubmit="return validateForm()">
        <input type="text" id="c_name" name="c_name" placeholder="Enter customer Name" 
               value="<?php echo isset($_GET['c_name']) ? htmlspecialchars($_GET['c_name']) : ''; ?>">
        <input type="text" id="c_mobile" name="c_mobile" placeholder="Enter customer Mobile" 
               value="<?php echo isset($_GET['c_mobile']) ? htmlspecialchars($_GET['c_mobile']) : ''; ?>">
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
                <tr>
                    <td>Last Visit Date</td>
                    <td><?php echo isset($last_visit_date) ? htmlspecialchars($last_visit_date) : 'N/A'; ?></td>
                </tr>
                <tr>
                    <td>Service History (Last 5)</td>
                    <td>
                        <?php
                        if (!empty($service_history)) {
                            foreach ($service_history as $service) {
                                echo htmlspecialchars($service['o_date']) . " - " . 
                                     htmlspecialchars($service['s_si_no']) . " (" . 
                                     htmlspecialchars($service['s_qty']) . " @ " . 
                                     htmlspecialchars($service['s_price']) . ")<br>";
                            }
                        } else {
                            echo "No service history available.";
                        }
                        ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
    <div class="form-buttons">
        <button id="previousButton">Previous</button>
        <button id="nextButton">Next</button>
        <form action="customer_modify.php" method="GET" style="display:inline;">
            <button type="submit" name="c_no" value="<?php echo htmlspecialchars($customer['c_no']); ?>">Modify</button>
        </form>
        <button id="deleteButton" onclick="deleteCustomer(<?php echo htmlspecialchars($customer['c_no']); ?>)">Delete</button>
        <a href="customer_list.php" id="cancelLink"><button type="button">Cancel</button></a>
    </div>
</div>

<script>
    document.getElementById('previousButton').addEventListener('click', function() {
        let currentIndex = parseInt(document.getElementById('currentIndex').value);
        currentIndex = currentIndex > 0 ? currentIndex - 1 : 0;
        window.location.href = "customer_view.php?index=" + currentIndex + "&c_name=<?php echo urlencode($c_name); ?>&c_mobile=<?php echo urlencode($c_mobile); ?>";
    });

    document.getElementById('nextButton').addEventListener('click', function() {
        let currentIndex = parseInt(document.getElementById('currentIndex').value);
        currentIndex = currentIndex + 1;
        window.location.href = "customer_view.php?index=" + currentIndex + "&c_name=<?php echo urlencode($c_name); ?>&c_mobile=<?php echo urlencode($c_mobile); ?>";
    });

    function deleteCustomer(c_no) {
        if (confirm('Are you sure you want to delete this customer?')) {
            window.location.href = "customer_delete.php?c_no=" + c_no;
        }
    }

  
    // Function to validate the form inputs
    function validateForm() {
        // Get input values
        const customerName = document.getElementById('c_name').value.trim();
        const customerMobile = document.getElementById('c_mobile').value.trim();
        
        // Regex patterns
        const namePattern = /^[A-Za-z\s]+$/;  // Only letters and spaces allowed
        const mobilePattern = /^\d+$/;  // Only numeric values allowed

        // Validate customer name
        if (customerName !== "" && !namePattern.test(customerName)) {
            alert("Customer name can only contain letters and spaces.");
            return false;
        }

        // Validate customer mobile
        if (customerMobile !== "" && !mobilePattern.test(customerMobile)) {
            alert("Customer mobile can only contain numeric values.");
            return false;
        }

        // If all validations pass, submit the form
        return true;
    }
</script>
</body>
</html>
