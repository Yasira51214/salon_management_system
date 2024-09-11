<?php
include 'common.php';

// Date input style start
$selected_date = isset($_GET['date']) ? $_GET['date'] : '';

// Convert the date format
$formatted_date = '';
if ($selected_date) {
    $date = DateTime::createFromFormat('Y-m-d', $selected_date);
    if ($date) {
        $formatted_date = $date->format('d-F-Y');
    } else {
        $formatted_date = htmlspecialchars($selected_date); // Fallback if date format is incorrect
    }
}
// Date style end

$search_query = isset($_GET['search']) ? $_GET['search'] : '';

// Check connection
function fetchData($conn, $selected_date, $search_query) {
    $data = [];
    $search_query_wildcard = "%$search_query%";

    // Prepare the SQL query for income
    $sql_orders = "
        SELECT o.o_c_no, c.c_name, os.s_si_no, si.si_cat, si.si_service_name, os.s_price, os.s_qty, (os.s_price * os.s_qty) AS amount
        FROM `order` o
        JOIN `customer` c ON o.o_c_no = c.c_no
        JOIN `orderservice` os ON o.o_no = os.s_o_no
        JOIN `service_item` si ON os.s_si_no = si.si_no
        WHERE o.o_date = ?
    ";

    // Append search query if it exists
    if (!empty($search_query)) {
        $sql_orders .= " AND (c.c_name LIKE ? OR si.si_service_name LIKE ? OR os.s_si_no LIKE ?)";
    }

    // Prepare and bind the statement
    $stmt = $conn->prepare($sql_orders);
    if (!$stmt) {
        die("Error preparing query: " . $conn->error);
    }

    if (!empty($search_query)) {
        $stmt->bind_param('ssss', $selected_date, $search_query_wildcard, $search_query_wildcard, $search_query_wildcard);
    } else {
        $stmt->bind_param('s', $selected_date);
    }

    // Execute the query
    $stmt->execute();
    $result_orders = $stmt->get_result();

    // Check if query was successful
    if ($result_orders === false) {
        die("Error executing query: " . $conn->error);
    }

    $income_total = 0;
    // Process the income data
    while ($row = $result_orders->fetch_assoc()) {
        $customer_name = $row['c_name'];
        if (!isset($data[$customer_name])) {
            $data[$customer_name] = [];
        }
        $service_name = $row['si_service_name'];
        $si_cat = $row['si_cat'];
        $price = $row['s_price'];
        $quantity = $row['s_qty'];
        $amount = $row['amount'];

        $data[$customer_name][] = [
            'service_name' => $service_name,
            'service_cat' => $si_cat,
            'price' => $price,
            'quantity' => $quantity,
            'amount' => $amount
        ];
        $income_total += $amount; // Sum up the income
    }

    // Prepare the SQL query for expenses
    $sql_expenses = "
        SELECT e.e_no, ec.ex_cat_name, e.e_description, e.e_price, e.e_qty, (e.e_price * e.e_qty) AS e_amount, e.e_exp_date 
        FROM `expense` e 
        JOIN `expense_cat` ec ON e.e_ex_no = ec.ex_no
        WHERE e.e_exp_date = ?
    ";

    // Append search query if it exists
    if (!empty($search_query)) {
        $sql_expenses .= " AND (ec.ex_cat_name LIKE ? OR e.e_description LIKE ?)";
    }

    // Prepare and bind the statement
    $stmt = $conn->prepare($sql_expenses);
    if (!$stmt) {
        die("Error preparing query: " . $conn->error);
    }

    if (!empty($search_query)) {
        $stmt->bind_param('ssss', $selected_date, $search_query_wildcard, $search_query_wildcard, $search_query_wildcard);
    } else {
        $stmt->bind_param('s', $selected_date);
    }

    // Execute the query
    $stmt->execute();
    $result_expenses = $stmt->get_result();

    // Check if query was successful
    if ($result_expenses === false) {
        die("Error executing query: " . $conn->error);
    }

    // Calculate total expenses
    $expense_total = 0;
    while ($row = $result_expenses->fetch_assoc()) {
        $expense_total += $row['e_amount']; // Sum up the expenses
    }

    // Close the statement
    $stmt->close();

    return [$data, $result_expenses, $income_total, $expense_total];
}


// Fetch data based on date and search query
list($data, $result_expenses,$income_total, $expense_total) = fetchData($conn, $selected_date, $search_query);
  $balance = $income_total-$expense_total;


// Fetch the currency setting starting
$sql = "SELECT ss_currency FROM currency WHERE ss_no = 1";
$result = mysqli_query($conn, $sql);

if (!$result) {
    die("SQL error: " . mysqli_error($conn));
}

$currency = 'RS'; // Default value
if ($row = mysqli_fetch_assoc($result)) {
    $currency = $row['ss_currency'];
}

// Fetch the currency setting ending

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Balance Sheet</title>
    <link rel="stylesheet" href="./css/balance_view.css">
    <style>
        /* Responsive Design */
        @media (max-width: 768px) {
            h1 {
                font-size: 24px;
            }
            .search-bar input[type="text"] {
                font-size: 14px;
            }
            .search-bar button {
                font-size: 14px;
                padding: 8px;
            }
            .balance-table th, .balance-table td {
                padding: 6px;
                font-size: 12px;
            }
            .footer-buttons button {
                font-size: 14px;
                padding: 8px 12px;
                
            }
        }
        .footer-buttons {
            display: flex;
            justify-content: flex-end; /* Aligns the buttons to the right */
            padding: 10px;
        }
        @media (max-width: 480px) {
            .container {
                width: 95%;
                padding: 10px;
            }
            h1 {
                font-size: 20px;
            }
            .search-bar input[type="text"] {
                font-size: 12px;
                padding: 6px;
            }
            .search-bar button {
                font-size: 12px;
                padding: 6px;
            }
            .balance-table th, .balance-table td {
                font-size: 10px;
                padding: 4px;
            }
            .balance-table {
                overflow-x: auto;
                display: block;
            }
            .footer-buttons button {
                font-size: 12px;
                padding: 6px 10px;
            }
        }

        @media (min-width: 769px) and (max-width: 1024px) {
            h1 {
                font-size: 28px;
            }
            .search-bar input[type="text"] {
                font-size: 16px;
            }
            .search-bar button {
                font-size: 16px;
                padding: 10px;
            }
            .balance-table th, .balance-table td {
                font-size: 14px;
                padding: 8px;
            }
            .footer-buttons button {
                font-size: 16px;
                padding: 10px 20px;
            }
        }

        @media (min-width: 1025px) {
            h1 {
                font-size: 32px;
            }
            .search-bar input[type="text"] {
                font-size: 18px;
            }
            .search-bar button {
                font-size: 18px;
                padding: 12px;
            }
            .balance-table th, .balance-table td {
                font-size: 16px;
                padding: 10px;
            }
            .footer-buttons button {
                font-size: 18px;
                padding: 12px 24px;
            }
        }
        /* Hide the sidebar menu when printing */
        @media print {
            /* Hide everything except the container */
            body * {
                visibility: hidden;
            }
            
            .container, .container * {
                visibility: visible;
            }

            /* Adjust the print layout */
            .container {
                position: absolute;
                left: 0;
                top: 0;
            }

            /* Optional: Adjust the page size and margins */
            @page {
                size: A4;
                margin: 20mm;
            }
        }

        .action {
            background-color: #22584b;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s;
            display: inline-block;
            margin-right: 5px;
        }

        .action a {
            color: white;
            text-decoration: none;
        }

        .action:hover {
            background-color: #1d4e42;
        }

        .action .update::before {
            content: "✎ ";
        }

        .action .delete::before {
            content: "✖ ";
        }
    </style>
    <script>
        function deleteExpense(e_no) {
            if (confirm('Are you sure you want to delete this expense?')) {
                window.location.href = 'expense_delete.php?id=' + e_no;
            }
        }
    </script>
</head>
<body>
<?php include 'side_bar.php'; ?>    
<div class="container">
    <h1>Balance Sheet View</h1>
    <br>
    <div class="search-bar">
        <form method="GET" action="">
            <input type="date" name="date" value="<?php echo htmlspecialchars($selected_date); ?>">
            <a><button type="submit" class="search-btn">Search</button></a>
        </form>
    </div>
    <h3 id="head"><?php echo htmlspecialchars($formatted_date); ?></h3>
    <h1 id="head">Income</h1>
    <table class="balance-table">
        <thead>
            <tr>
                <th>Customer Name</th>
                <th>Category</th>
                <th>Service Name</th>
                <th>Price</th>
                <th>Qty</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (!empty($data)) {
                foreach ($data as $customer_name => $services) {
                    $rowspan = count($services);
                    $first = true;
                    foreach ($services as $service) {
                        echo "<tr>";
                        if ($first) {
                            echo "<td rowspan='$rowspan'>" . htmlspecialchars($customer_name) . "</td>";
                            $first = false;
                        }
                        echo "<td>" . htmlspecialchars($service['service_cat']) . "</td>";
                        echo "<td>" . htmlspecialchars($service['service_name']) . "</td>";
                        echo "<td>" . htmlspecialchars($service['price']) . "</td>";
                        echo "<td>" . htmlspecialchars($service['quantity']) . "</td>";
                        echo "<td>" . htmlspecialchars($service['amount']) . "</td>";
                        echo "</tr>";
                    }
                }
            } else {
                echo "<tr><td colspan='5'>No data available</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <h1 id="head">Expense</h1>
<table class="balance-table">
    <thead>
        <tr>
            <th>Date</th>
            <th>Category</th>
            <th>Description</th>
            <th>Price</th>
            <th>Qty</th>
            <th>Amount</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
    <?php
if ($result_expenses->num_rows > 0) {
    while ($row = $result_expenses->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['e_exp_date']) . "</td>"; // Display expense date
        echo "<td>" . htmlspecialchars($row['ex_cat_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['e_description']) . "</td>";
        echo "<td>" . htmlspecialchars($row['e_price']) . "</td>";
        echo "<td>" . htmlspecialchars($row['e_qty']) . "</td>";
        echo "<td>" . htmlspecialchars($row['e_amount']) . "</td>";
        echo "<td>
                <button class='action update'><a href='expense_modify.php?id=" . htmlspecialchars($row['e_no']) . "'>Update</a></button>
                <button class='action delete' onclick='deleteExpense(" . htmlspecialchars($row['e_no']) . ")'>Delete</button>
              </td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='7'>No data available</td></tr>"; // Adjust column span
}
?>
    </tbody>
</table>

<br>
  <!-- Final Balance  -->
  <h1 id="head">Balance Sheet</h1>
    <table class="balance-table">
        <thead>
            <tr>
                <th>Income</th>
                <th>Expense</th>
                <th>Balance</th>
            </tr>
        </thead>
        <tbody>
        <tr>
                <td><?php echo htmlspecialchars($income_total); ?> &nbsp;<?= htmlspecialchars($currency) ?></td>
                <td><?php echo htmlspecialchars($expense_total); ?> &nbsp;<?= htmlspecialchars($currency) ?></td>
                <td><?php echo htmlspecialchars($balance); ?> &nbsp;<?= htmlspecialchars($currency) ?></td>
            </tr>
        </tbody>
    </table>
    <?php $conn->close(); ?>

    <div class="footer-buttons">
    <a href="expense_add.php?date=<?php echo urlencode($selected_date); ?>">
    <button class="action">Expense Add</button>
</a>
        <button onclick="window.print();" class="print-btn">Print</button>
        <button onclick="window.history.back();" class="back-btn">Back</button>
    </div>
</div>

</body>
</html>