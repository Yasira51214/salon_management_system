<?php
include 'common.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Table with Pagination and Filters</title>
    <link rel="stylesheet" href="./css/feedback_statistic.css">
</head>
<body>
<?php include "side_bar.php"; ?>

<div class="container">
    <h1>Feedback Statistic</h1><br>
    <div class="filters">
        <form method="GET" action="">
            <select name="category">
                <option value="all" <?= isset($_GET['category']) && $_GET['category'] == 'all' ? 'selected' : '' ?>>All</option>
                <option value="N" <?= isset($_GET['category']) && $_GET['category'] == 'N' ? 'selected' : '' ?>>Nail</option>
                <option value="P" <?= isset($_GET['category']) && $_GET['category'] == 'P' ? 'selected' : '' ?>>Pedicure</option>
                <option value="E" <?= isset($_GET['category']) && $_GET['category'] == 'E' ? 'selected' : '' ?>>Eyelashe</option>
                <option value="M" <?= isset($_GET['category']) && $_GET['category'] == 'M' ? 'selected' : '' ?>>Massage</option>
                <option value="T" <?= isset($_GET['category']) && $_GET['category'] == 'T' ? 'selected' : '' ?>>Training</option>
                <option value="S" <?= isset($_GET['category']) && $_GET['category'] == 'S' ? 'selected' : '' ?>>Sales item</option>
            </select>

            <select name="rate">
                <option value="all" <?= isset($_GET['rate']) && $_GET['rate'] == 'all' ? 'selected' : '' ?>>Rate</option>
                <option value="1" <?= isset($_GET['rate']) && $_GET['rate'] == '1' ? 'selected' : '' ?>>1</option>
                <option value="2" <?= isset($_GET['rate']) && $_GET['rate'] == '2' ? 'selected' : '' ?>>2</option>
                <option value="3" <?= isset($_GET['rate']) && $_GET['rate'] == '3' ? 'selected' : '' ?>>3</option>
                <option value="4" <?= isset($_GET['rate']) && $_GET['rate'] == '4' ? 'selected' : '' ?>>4</option>
                <option value="5" <?= isset($_GET['rate']) && $_GET['rate'] == '5' ? 'selected' : '' ?>>5</option>
            </select>

            <input type="text" name="search" value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>" placeholder="Search...">
            <button type="submit">Search</button>
        </form>
    </div>

    <table id="reviewsTable">
        <thead>
            <tr>
                <th>No#</th>
                <th>Category</th>
                <th>Service</th>
                <th>Rate</th>
                <th>Review</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            $categoryFilter = isset($_GET['category']) ? $conn->real_escape_string($_GET['category']) : 'all';
            $rateFilter = isset($_GET['rate']) ? $conn->real_escape_string($_GET['rate']) : 'all';
            $search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
            $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
            $rowsPerPage = 10;
            $offset = ($page - 1) * $rowsPerPage;

            // Map category codes to full names
            $categoryFullNames = [
                'N' => 'Nail',
                'P' => 'Pedicure',
                'E' => 'Eyelashe',
                'M' => 'Massage',
                'T' => 'Training',
                'S' => 'Sales item'
            ];

            // Calculate total rows
            $totalSql = "SELECT COUNT(*) as total 
                         FROM feedback 
                         JOIN orderservice os ON feedback.f_c_no = os.s_order_no
                         JOIN service_item si ON os.s_si_no = si.si_no
                         WHERE 1=1";

            if ($categoryFilter !== 'all') {
                $totalSql .= " AND si.si_cat = '$categoryFilter'";
            }

            if (!empty($search)) {
                $totalSql .= " AND (si.si_service_name LIKE '%$search%' 
                             OR feedback.f_review1 LIKE '%$search%' 
                             OR feedback.f_review2 LIKE '%$search%' 
                             OR feedback.f_review3 LIKE '%$search%' 
                             OR feedback.f_review4 LIKE '%$search%' 
                             OR feedback.f_review5 LIKE '%$search%' 
                             OR feedback.f_review6 LIKE '%$search%' 
                             OR feedback.f_review7 LIKE '%$search%' 
                             OR feedback.f_review8 LIKE '%$search%' 
                             OR feedback.f_review9 LIKE '%$search%' 
                             OR feedback.f_review10 LIKE '%$search%')";
            }

            if ($rateFilter !== 'all') {
                $rateFilterValue = (int)$rateFilter;
                $totalSql .= " AND (feedback.f_rate1 = $rateFilterValue 
                             OR feedback.f_rate2 = $rateFilterValue 
                             OR feedback.f_rate3 = $rateFilterValue 
                             OR feedback.f_rate4 = $rateFilterValue 
                             OR feedback.f_rate5 = $rateFilterValue 
                             OR feedback.f_rate6 = $rateFilterValue 
                             OR feedback.f_rate7 = $rateFilterValue 
                             OR feedback.f_rate8 = $rateFilterValue 
                             OR feedback.f_rate9 = $rateFilterValue 
                             OR feedback.f_rate10 = $rateFilterValue)";
            }

            $totalResult = $conn->query($totalSql);
            if ($totalResult === false) {
                die("SQL error: " . $conn->error);
            }

            $totalRows = $totalResult->fetch_assoc()['total'];

            // Updated SQL query with ORDER BY clause for descending order
            $sql = "SELECT feedback.*, os.s_si_no, os.s_cat, si.si_service_name, si.si_cat
                    FROM feedback 
                    JOIN orderservice os ON feedback.f_c_no = os.s_order_no
                    JOIN service_item si ON os.s_si_no = si.si_no
                    WHERE 1=1";

            if ($categoryFilter !== 'all') {
                $sql .= " AND si.si_cat = '$categoryFilter'";
            }

            if (!empty($search)) {
                $sql .= " AND (si.si_service_name LIKE '%$search%' 
                             OR feedback.f_review1 LIKE '%$search%' 
                             OR feedback.f_review2 LIKE '%$search%' 
                             OR feedback.f_review3 LIKE '%$search%' 
                             OR feedback.f_review4 LIKE '%$search%' 
                             OR feedback.f_review5 LIKE '%$search%' 
                             OR feedback.f_review6 LIKE '%$search%' 
                             OR feedback.f_review7 LIKE '%$search%' 
                             OR feedback.f_review8 LIKE '%$search%' 
                             OR feedback.f_review9 LIKE '%$search%' 
                             OR feedback.f_review10 LIKE '%$search%')";
            }

            // Add ORDER BY clause here. For example, sorting by `feedback.f_no` in descending order
            $sql .= " ORDER BY feedback.f_no DESC LIMIT $offset, $rowsPerPage";

            $result = $conn->query($sql);

            if ($result === false) {
                die("SQL error: " . $conn->error);
            }
            
            // Adjust counter to start from the highest number for the current page
            $counter = $totalRows - $offset;

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $reviews = [];
                    for ($i = 1; $i <= 1; $i++) {
                        if (!empty($row["f_review$i"])) {
                            $reviews[] = $row["f_review$i"];
                        }
                    }
        
                    $categoryCode = $row['si_cat']; // Get category code from service_item
                    $service = $row['si_service_name']; // Get service name from service_item
                    $categoryFullName = isset($categoryFullNames[$categoryCode]) ? $categoryFullNames[$categoryCode] : 'Unknown'; // Map code to full name
        
                    for ($i = 1; $i <= 1; $i++) {
                        if (isset($row["f_rate$i"])) {
                            echo "<tr>
                                    <td>$counter</td>
                                    <td data-label='Category'>{$categoryFullName}</td>
                                    <td data-label='Service'>{$service}</td>
                                    <td data-label='Rate'>{$row["f_rate$i"]}</td>
                                    <td data-label='Review'>" . (isset($reviews[$i - 1]) ? $reviews[$i - 1] : 'No review') . "</td>
                                  </tr>";
                            $counter--;
                        }
                    }
                }
            } else {
                echo "<tr><td colspan='5'>No records found</td></tr>";
            }

            $conn->close();
            ?>
        </tbody>
    </table>

    <div class="pagination">
        <?php
        // Calculate total pages
        $totalPages = ceil($totalRows / $rowsPerPage);

        // Previous page button
        if ($page > 1) {
            echo "<a href='?page=" . ($page - 1) . "&category=" . htmlspecialchars($categoryFilter) . "&rate=" . htmlspecialchars($rateFilter) . "&search=" . htmlspecialchars($search) . "'><button class='page-btn'>« Previous Page</button></a>";
        }

        // Pagination links
        for ($i = 1; $i <= $totalPages; $i++) {
            echo "<a href='?page=$i&category=" . htmlspecialchars($categoryFilter) . "&rate=" . htmlspecialchars($rateFilter) . "&search=" . htmlspecialchars($search) . "'><button class='page-btn " . ($i == $page ? 'active' : '') . "'>$i</button></a>";
        }

        // Next page button
        if ($page < $totalPages) {
            echo "<a href='?page=" . ($page + 1) . "&category=" . htmlspecialchars($categoryFilter) . "&rate=" . htmlspecialchars($rateFilter) . "&search=" . htmlspecialchars($search) . "'><button class='page-btn'>Next Page »</button></a>";
        }
        ?>
    </div>
</div>
</body>
</html>
