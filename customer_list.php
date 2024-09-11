<?php
include 'common.php'; // Ensure this includes database connection
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Management</title>
    <link rel="stylesheet" href="./css/customer_list.css">
    <style>
        @media (max-width: 768px) {
            .container {
                width: 100%;
                padding: 10px;
            }
            .search-bar {
                flex-direction: column;
                align-items: stretch;
            }
            .search-bar input[type="text"],
            .search-bar button {
                width: 100%;
                margin-bottom: 5px;
            }
            .customer-table th, .customer-table td {
                padding: 10px;
                font-size: 11px;
            }
            .form-buttons {
                flex-direction: column;
                gap: 10px;
            }
            .form-buttons button {
                width: 100%;
            }
            .pagination {
                flex-direction: row;
                justify-content: center;
            }
            .pagination a {
                width: auto;
                margin: 5px;
            }
        }
        @media (min-width: 768px) and (max-width: 1024px) {
            .container {
                width: 80%;
            }
            .search-bar input[type="text"] {
                flex-grow: 0;
            }
            .form-buttons button {
                width: 30%;
                margin-bottom: 0;
                padding: 10px;
                font-size: 14px;
            }
        }
        @media (min-width: 1024px) {
            .container {
                width: 70%;
            }
        }
    </style>
</head>
<body>
<?php include 'side_bar.php'; ?>  
<div class="container">
    <h1>Customer List</h1>
    <br>
    <div class="search-bar">
    <form action="customer_list.php" method="GET" onsubmit="return validateForm()">
        <input type="text" id="c_name" name="c_name" placeholder="Enter customer Name" 
               value="<?php echo isset($_GET['c_name']) ? htmlspecialchars($_GET['c_name']) : ''; ?>">
        <input type="text" id="c_mobile" name="c_mobile"  placeholder="Enter customer Mobile" 
               value="<?php echo isset($_GET['c_mobile']) ? htmlspecialchars($_GET['c_mobile']) : ''; ?>">
        <button type="submit">Search</button>
        <a href="register.php"><button type="button">Add</button></a>
    </form>
</div>
    <?php
    ini_set('display_errors', 1);
    ini_set('log_errors', 1);
    error_reporting(E_ALL);

    // Function to format mobile numbers
    function formatMobileNumber($number) {
        return substr($number, 0, 4) . '-' . substr($number, 4);
    }

    // Initialize the query
    $sql = "SELECT * FROM customer WHERE 1=1";
    $c_name = $c_mobile = '';

    // Append conditions based on the input
    if (isset($_GET['c_name']) && !empty($_GET['c_name'])) {
        $c_name = mysqli_real_escape_string($conn, $_GET['c_name']);
        $sql .= " AND c_name LIKE '%$c_name%'";
    }
    if (isset($_GET['c_mobile']) && !empty($_GET['c_mobile'])) {
        $c_mobile = mysqli_real_escape_string($conn, $_GET['c_mobile']);
        $sql .= " AND c_mobile = '$c_mobile'";
    }

    // Sort by registration date in descending order (new customers at the top)
    $sql .= " ORDER BY c_reg_date DESC";

    // Pagination logic
    $results_per_page = 10; // Number of results per page
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        die("Query Unsuccessful: " . mysqli_error($conn));
    }
    $total_results = mysqli_num_rows($result);
    $total_pages = ceil($total_results / $results_per_page);

    // Determine the current page
    $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $current_page = ($current_page < 1) ? 1 : $current_page;
    $current_page = ($current_page > $total_pages) ? $total_pages : $current_page;

    // Determine the starting limit number
    $start_limit = ($current_page - 1) * $results_per_page;
    $sql .= " LIMIT $start_limit, $results_per_page";
    $result = mysqli_query($conn, $sql) or die("Query Unsuccessful: " . mysqli_error($conn));

    $row_number = $total_results - $start_limit;

    // Pagination range
    $pages_to_show = 5;
    $start_page = max(1, $current_page - floor($pages_to_show / 2));
    $end_page = min($total_pages, $start_page + $pages_to_show - 1);

    if ($end_page - $start_page + 1 < $pages_to_show) {
        $start_page = max(1, $end_page - $pages_to_show + 1);
    }
    ?>

    <table class="customer-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Mobile</th>
                <th>Category</th>
                <th>Status</th>
            </tr> 
        </thead>
        <tbody>
        <?php
        $row_number = $total_results - $start_limit;

        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                // Check if the customer is removed and set the row class accordingly
                $row_class = ($row['c_is_del'] == 1) ? 'removed' : '';
                $status_display = ($row['c_is_del'] == 1) ? 'Removed' : 'Active'; // Display "Removed" or "Active"
        
                echo "<tr class='$row_class'>
                        <td>" . $row_number . "</td>
                        <td><a href='customer_view.php?c_id=" . urlencode($row['c_no']) . "'>" . htmlspecialchars($row['c_name']) . "</a></td>
                        <td>" . formatMobileNumber(htmlspecialchars($row['c_mobile'])) . "</td>
                        <td>" . htmlspecialchars($row['c_cat']) . "</td>
                        <td>" . htmlspecialchars($status_display) . "</td>
                    </tr>";
                $row_number--;
            }
        } else {
            echo "<tr><td colspan='5'>No data available</td></tr>";
        }
        ?>
        </tbody>
    </table>
    <div class="pagination">
        <?php if ($current_page > 1): ?>
            <a href="?page=<?php echo $current_page - 1; ?>&c_name=<?php echo htmlspecialchars($c_name); ?>&c_mobile=<?php echo htmlspecialchars($c_mobile); ?>"><button class="page-btn">« Previous Page</button></a>
        <?php endif; ?>
        <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
            <a href="?page=<?php echo $i; ?>&c_name=<?php echo htmlspecialchars($c_name); ?>&c_mobile=<?php echo htmlspecialchars($c_mobile); ?>"><button class="page-btn <?php echo ($i == $current_page) ? 'active' : ''; ?>"><?php echo $i; ?></button></a>
        <?php endfor; ?>
        <?php if ($current_page < $total_pages): ?>
            <a href="?page=<?php echo $current_page + 1; ?>&c_name=<?php echo htmlspecialchars($c_name); ?>&c_mobile=<?php echo htmlspecialchars($c_mobile); ?>"><button class="page-btn">Next Page »</button></a>
        <?php endif; ?>
    </div>
</div>

<script>
   
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
