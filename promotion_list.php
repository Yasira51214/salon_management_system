<?php
include 'common.php';

// Define how many records you want per page
$records_per_page = 10;

// Get the current page number from the URL, default to 1 if not set
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = ($page > 0) ? $page : 1;

// Calculate the starting record for the current page
$start_from = ($page - 1) * $records_per_page;

// Fetch the total number of promotions
$sql = "SELECT COUNT(*) AS total FROM promotion";
$result = $conn->query($sql);
$total_records = $result->fetch_assoc()['total'];

// Calculate total pages
$number_of_pages = ceil($total_records / $records_per_page);

// Fetch promotions for the current page
$sql = "SELECT p_no, p_name, p_s_date, p_e_date, p_s_items, p_rate_price, status FROM promotion LIMIT $start_from, $records_per_page";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Promotion List</title>
    <link rel="stylesheet" href="css/promotion_list.css">
    <script>
        // Confirmation function for Activate/Deactivate
        function confirmAction(p_no, action) {
            var message = "Are you sure you want to " + action + " this promotion?";
            if (confirm(message)) {
                // If confirmed, submit the form with the selected action
                document.getElementById('statusForm-' + p_no).submit();
            } else {
                // Reset the select input to its previous value
                document.getElementById('status_' + p_no).value = action === 'Activate' ? 'Deactivate' : 'Activate';
            }
        }
    </script>
</head>
<body>
    <?php include "side_bar.php"; ?>
    <div class="container">
        <h1>Promotion List</h1>
        <a href="./promotion_add.php"><button class="add">Add Promotion</button></a>
        <table class="customer-table">
            <style>
          /* Responsive Styles */
          @media (max-width: 1024px) {
            .customer-table th, .customer-table td {
                font-size: 14px;
                padding: 6px;
            }

            .add {
                width: 100%;
                margin-bottom: 20px;
                text-align: center;
            }

            .page-btn {
                padding: 5px;
            }
        }

        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }

            .customer-table th, .customer-table td {
                display: block;
                width: 100%;
                box-sizing: border-box;
            }

            .customer-table tr {
                display: block;
                margin-bottom: 10px;
            }

            .customer-table td::before {
                content: attr(data-label);
                font-weight: bold;
                display: block;
                margin-bottom: 5px;
            }

            .customer-table td {
                text-align: right;
                padding-left: 50%;
            }

            .add {
                width: 100%;
                margin-bottom: 20px;
            }

            .page-btn {
                padding: 5px;
                width: 100%;
                margin-bottom: 10px;
            }

            .pagination .page-btn {
                display: block;
                margin: 10px 0;
            }
        }

            </style>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Promo Name</th>
                    <th>Promo Start Date</th>
                    <th>Promo End Date</th>
                    <th>Selected Items</th>
                    <th>Rate/Price</th>
                    <th>Active/Not</th>
                    <th>Function</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    $counter = $start_from + 1; // Counter for the serial number
                    while ($row = $result->fetch_assoc()) {
                        $p_no = $row['p_no'];
                        $p_name = htmlspecialchars($row['p_name']);
                        $p_s_date = date('d-M-Y', strtotime($row['p_s_date']));
                        $p_e_date = date('d-M-Y', strtotime($row['p_e_date']));
                        $p_s_items = htmlspecialchars($row['p_s_items']);
                        $p_rate_price = htmlspecialchars($row['p_rate_price']);
                        $current_status = (int)$row['status']; // Fetch current status as integer

                        echo "<tr>";
                        echo "<td>{$counter}</td>";
                        echo "<td><a href='./promotion_view.php?p_no={$p_no}'>{$p_name}</a></td>"; // Make p_name a clickable link
                        echo "<td>{$p_s_date}</td>";
                        echo "<td>{$p_e_date}</td>";
                        echo "<td>{$p_s_items}</td>";
                        echo "<td>{$p_rate_price}</td>";
                        echo "<td>";
                        
                        // Status Form
                        echo "<form id='statusForm-{$p_no}' method='POST' action='./update_status.php'>";
                        echo "<input type='hidden' name='p_no' value='{$p_no}'>";
                        echo "<select class='page-btn' name='status' id='status_{$p_no}' onchange=\"confirmAction('{$p_no}', this.value)\">";
                        echo "<option value='1'" . ($current_status === 1 ? ' selected' : '') . ">Activate</option>";
                        echo "<option value='0'" . ($current_status === 0 ? ' selected' : '') . ">Deactivate</option>";
                        echo "</select>";
                        echo "</form>";
                        
                        echo "</td>";
                        echo "<td>";
                        echo "<div style='display: flex;'>"; // Container to hold both buttons
                        echo "<a href='./promotion_modify.php?p_no={$p_no}'><button class='page-btn'>Modify</button></a>";
                        echo "<form method='POST' action='./delete_promotion.php' onsubmit='return confirm(\"Are you sure you want to delete this promotion?\");'>"; // Fix onsubmit attribute
                        echo "<button type='submit' class='page-btn'>Delete</button>";
                        echo "<input type='hidden' name='p_no' value='{$p_no}'>";
                        echo "</form>";
                        echo "</div>"; // Close the div
                        echo "</td>";
                        echo "</tr>";
                        
                        $counter++;
                    }
                } else {
                    echo "<tr><td colspan='8'>No promotions found.</td></tr>";
                }

                $conn->close();
                ?>
            </tbody>
        </table>
         
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?>"><button class="page-btn">« Previous Page</button></a>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $number_of_pages; $i++): ?>
                <a href="?page=<?php echo $i; ?>"><button class="page-btn <?php echo ($i == $page) ? 'active' : ''; ?>"><?php echo $i; ?></button></a>
            <?php endfor; ?>
            <?php if ($page < $number_of_pages): ?>
                <a href="?page=<?php echo $page + 1; ?>"><button class="page-btn">Next Page »</button></a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
