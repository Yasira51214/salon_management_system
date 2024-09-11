<?php
include 'common.php';

// Function to truncate a string to a specified number of words
function truncateDescription($description, $wordLimit = 10) {
    $words = explode(' ', $description);
    if (count($words) > $wordLimit) {
        return implode(' ', array_slice($words, 0, $wordLimit)) . '...';
    }
    return $description;
}
// Function to get the effective price based on promotion
function getEffectivePrice($service_no, $conn) {
    $current_date = date('Y-m-d');

    // Get the promotion details
    $stmt = $conn->prepare("
        SELECT p.p_s_date, p.p_e_date, p.status, si.si_price, ps.pro_s_price
        FROM promotion p
        JOIN pro_service ps ON p.p_no = ps.pro_p_no
        JOIN service_item si ON ps.pro_si_no = si.si_no
        WHERE ps.pro_si_no = ? 
        LIMIT 1
    ");
    $stmt->bind_param("i", $service_no);
    $stmt->execute();
    $result = $stmt->get_result();
    $promotion = $result->fetch_assoc();
    $stmt->close();

    if ($promotion) {
        if ($promotion['status'] == 1 && $current_date >= $promotion['p_s_date'] && $current_date <= $promotion['p_e_date']) {
            // Promotion is active, update the price to the promotion price
            $update_stmt = $conn->prepare("UPDATE service_item SET si_promotion_price = ? WHERE si_no = ?");
            $update_stmt->bind_param("di", $promotion['pro_s_price'], $service_no);
            $update_stmt->execute();
            $update_stmt->close();
            
            return $promotion['pro_s_price']; // Return the promotion price
        } 
    }

    // If no promotion is found, return the regular price (shouldn't happen if the logic is correct)
    return null;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Table</title>
    <link rel="stylesheet" href="./css/service_list.css">
 
    <style>
    /* Responsive styles */
    @media (max-width: 768px) {
        h1 {
            font-size: 30px;
        }
        .header {
            flex-direction: column;
            align-items: stretch;
        }
        .header select, .header input, .header button {
            font-size: 14px;
            padding: 6px;
        }

        .new-service {
            width: 100%;
            margin-top: 10px;
        }

        table th, table td {
            padding: 8px;
            width: 100%;
        }

        td img {
            width: 20px;
            height: 20px;
        }

        .description-cell {
            font-size: 12px;
        }
    }

    @media (max-width: 480px) {
        h1 {
            font-size: 24px;
        }
        .header select, .header input, .header button {
            font-size: 12px;
            padding: 4px;
        }
        .new-service {
            padding: 8px 16px;
            font-size: 14px;
        }

        table th, table td {
            padding: 6px;
            font-size: 12px;
        }

        td img {
            width: 30px;
            height: 30px;
        }

        .description-cell {
            font-size: 10px;
        }

        .pagination button {
            font-size: 12px;
            padding: 6px 12px;
        }
    }
    </style>
    <script>
        function confirmDelete(serviceId) {
            if (confirm("Do you want to delete this service?")) {
                window.location.href = "delete_service.php?id=" + serviceId;
            }
        }
    </script>
</head>
<body>
<?php include "side_bar.php"; ?>

<div class="container">
    <h1>Service List</h1>
    <br>
    <div class="header">
        <form method="GET" action="service_list.php">
            <select name="category">
                <option value="all">All</option>
                <option value="N">Nail</option>
                <option value="P">Pedi</option>
                <option value="E">Eyelashes</option>
                <option value="M">Massage</option>
                <option value="T">Training</option>
                <option value="S">Sales item</option>
            </select>
            <input type="text" name="search" id="search" placeholder="Search services..." oninput="validateSearchInput()">
            <button type="submit" style="color:22584b;">Search</button>
        </form>
        <a href="service_new.php"><button class="new-service">New Service</button></a>
    </div>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Category</th>
                <th>Service Name</th>
                <th>Price</th>
                <th>Pro Price</th>
                <th>Image</th>
                <th>Description</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Define the category mapping
            $category_map = [
                'S' => 'Sales item',
                'N' => 'Nail',
                'P' => 'Pedicure',
                'E' => 'Eyelashes',
                'M' => 'Massage',
                'T' => 'Training'
            ];

            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Determine the current page
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $results_per_page = 10;
            $offset = ($page - 1) * $results_per_page;

            // Ensure the offset is not negative
            if ($offset < 0) {
                $offset = 0;
            }

            // Get selected category
            $category = isset($_GET['category']) ? $_GET['category'] : 'all';
            $search = isset($_GET['search']) ? $_GET['search'] : '';

            // Build the query based on category and search term
            $conditions = [];
            if ($category !== 'all') {
                $conditions[] = "si_cat = '$category'";
            }
            if ($search !== '') {
                $conditions[] = "(si_service_name LIKE '%$search%' OR si_description LIKE '%$search%')";
            }

            $where_clause = count($conditions) > 0 ? 'WHERE ' . implode(' AND ', $conditions) : '';

            // Get total number of records
            $total_sql = "SELECT COUNT(*) AS total FROM service_item $where_clause";
            $total_result = $conn->query($total_sql);

            if ($total_result) {
                $total_row = $total_result->fetch_assoc();
                $total_records = $total_row['total'];
                $total_pages = ceil($total_records / $results_per_page);
            } else {
                echo "<tr><td colspan='8'>Error: " . $conn->error . "</td></tr>";
                exit();
            }

            function truncateServiceName($serviceName, $wordLimit = 3) {
                $words = explode(' ', $serviceName);
                if (count($words) > $wordLimit) {
                    return implode(' ', array_slice($words, 0, $wordLimit)) . '...';
                }
                return $serviceName;
            }
            // Fetch records with limit and offset, ordered by si_no in descending order
                $sql = "SELECT si_no, si_cat, si_service_name, si_price, si_promotion_price, si_image1, si_image2, si_image3, si_description 
                FROM service_item $where_clause 
                ORDER BY si_no DESC 
                LIMIT $results_per_page 
                OFFSET $offset";
                $result = $conn->query($sql);
            // Start display order from the current highest number based on offset and results per page
                $display_order = $total_records - $offset;
                if ($result) {
                    if ($result->num_rows > 0) {
                        // Output data of each row
                        while($row = $result->fetch_assoc()) {
                            $effective_price = getEffectivePrice($row['si_no'], $conn); // Get the effective price based on promotion
                            $truncatedServiceName = truncateServiceName($row["si_service_name"]); // Truncate service name
                
                            echo "<tr>";
                            echo "<td>" . $display_order . "</td>";
                            echo "<td>" . $category_map[$row["si_cat"]] . "</td>";
                            $truncatedServiceName = truncateServiceName($row["si_service_name"]);
                            echo '<td><a href="service_view.php?id=' . $row["si_no"] . '">' . $truncatedServiceName . '</a></td>';
                            echo "<td>" . $row["si_price"] . "</td>";
                            echo "<td>" . $effective_price . "</td>"; // Display the effective price
                            echo '<td><img src="' . $row["si_image1"] . '" alt="Service Image"></td>';
                            echo "<td class='description-cell'>" . truncateDescription($row["si_description"], 3) . "</td>";
                            echo '<td style="display: flex;"><button class="del" onclick="confirmDelete(' . $row["si_no"] . ')">Delete</button> <button class="modify">
                                   <a href="./service_modify.php?id='. $row["si_no"] . '">Modify</a></button></td>';
                            echo "</tr>";
                            $display_order--; // Decrease the display order
                        }
                    } else {
                        echo "<tr><td colspan='8'>No results found</td></tr>";
                    }
                } else {
                    echo "<tr><td colspan='8'>Error: " . $conn->error . "</td></tr>";
                }

            $conn->close();
            ?>
        </tbody>
    </table>
    <div class="pagination">
        <?php
        // Generate pagination buttons
        if ($page > 1) {
            echo '<a href="service_list.php?page=' . ($page - 1) . '&category=' . $category . '&search=' . $search . '"><button>&laquo; Previous Page</button></a>';
        }

        for ($i = 1; $i <= $total_pages; $i++) {
            if ($i == $page) {
                echo '<button class="active">' . $i . '</button>';
            } else {
                echo '<a href="service_list.php?page=' . $i . '&category=' . $category . '&search=' . $search . '"><button>' . $i . '</button></a>';
            }
        }

        if ($page < $total_pages) {
            echo '<a href="service_list.php?page=' . ($page + 1) . '&category=' . $category . '&search=' . $search . '"><button>Next Page &raquo;</button></a>';
        }
        ?>
    </div>
    <script>
        function validateSearchInput() {
            var searchInput = document.getElementById("search");
            var searchPattern = /^[A-Za-z\s]*$/;

            // Replace any non-letter and non-space characters with an empty string
            if (!searchPattern.test(searchInput.value)) {
                searchInput.value = searchInput.value.replace(/[^A-Za-z\s]/g, '');
                alert("Only letters and spaces are allowed.");
            }
        }
    </script>
</div>
</body>
</html>
