<?php
include 'common.php';
include "side_bar.php";

$customers = [];
$customerFound = false;
$searchName = '';
$searchMobile = '';
$searchDate = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_GET['search'])) {
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $mobile = isset($_POST['mobile']) ? $_POST['mobile'] : '';
    $date = isset($_POST['date']) ? $_POST['date'] : '';

    // Query to search customers by name and order by c_reg_date in descending order
    $sql = "SELECT c_no, c_name, c_mobile, c_reg_date FROM customer WHERE c_name LIKE ? ORDER BY c_reg_date DESC";
    $stmt = $conn->prepare($sql);
    $likeName = "%" . $name . "%";
    $stmt->bind_param('s', $likeName);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if customer is found
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $customers[] = $row;
            $customerFound = true;
        }
    } else {
        $customerFound = false;
    }

    $stmt->close();
    $searchName = htmlspecialchars($name);
    $searchMobile = htmlspecialchars($mobile);
    $searchDate = htmlspecialchars($date);
}


// Example of handling booking history for a specific customer
$selectedCustomerId = isset($_GET['customer_id']) ? intval($_GET['customer_id']) : null;
if ($selectedCustomerId) {
    $bookings = getCustomerBookingHistory($selectedCustomerId, $conn);
}
// Function to fetch order names based on date and customer details
function getOrderNamesByDate($date, $conn) {
    $orders = [];
    $sql = "SELECT o.o_no, o.o_c_no, c.c_name
            FROM `order` o
            JOIN customer c ON o.o_c_no = c.c_no
            WHERE o.o_date = ? ORDER BY o.o_no DESC";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }
    $stmt->bind_param('s', $date);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }

    $stmt->close();
    return $orders;
}

$currentMonth = isset($_GET['month']) ? (int)$_GET['month'] : (new DateTime())->format('n') - 1;
$currentYear = isset($_GET['year']) ? (int)$_GET['year'] : (new DateTime())->format('Y');

// Pagination setup
$limit = 5; // Number of records per page
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
$countResult = $conn->query($countQuery);
$totalRecords = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalRecords / $limit);

// Define the query with optional filters, ordering, and pagination
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
if (!empty($searchName)) {
    $query .= " AND c.c_name LIKE '%$searchName%'";
}
if (!empty($searchMobile)) {
    $query .= " AND c.c_mobile LIKE '%$searchMobile%'";
}
if (!empty($searchDate)) {
    $query .= " AND o.o_date = '$searchDate'";
}

$query .= " GROUP BY o.o_no ORDER BY o.o_date DESC LIMIT $limit OFFSET $offset";

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
                'services' => []
            ];
        }
        // Append the service name to the order's services
        $bookings[$o_no]['services'][] = $row['si_service_name'];
    }
}

// Convert the associative array to a numerically indexed array for easier use in the template
$bookings = array_values($bookings);
// Function to generate a random color
function generateRandomColor() {
    return sprintf('#%06X', mt_rand(0, 0xFFFFFF));
}
?>





<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Calendar</title>
    <link rel="stylesheet" href="./css/feedback_main.css">
    <!-- <link rel="stylesheet" href="./css/booking_history.css"> -->
    <style>
        .highlighted {
            background-color: #ef5a9b; 
            color: white; 
            font-weight: bold;
        }
        .hidden {
            display: none;
        }

        /* Modal styles */
        .modal {
            display: none; 
            position: fixed; 
            z-index: 1; 
            left: 0;
            top: 0;
            width: 100%; 
            height: 100%; 
            overflow: auto; 
            background-color:rgba(0,0,0,0.4);  
            background-color: rgba(0,0,0,0.4); 
            padding-top: 60px; 
        }
        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width:600px;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
        .modal-table {
            width: 100%;
            border-collapse: collapse;
        }
        .modal-table th, .modal-table td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        .modal-table th {
            background-color:#ef5a9b;
            color: white;
        }
        .calendar-day {
            position: relative;
        }
        .customer-initial {
            display: inline-block;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            text-align: center;
            line-height: 20px;
            color: white;
            font-weight: bold;
            margin: 2px;
            cursor: pointer;
        }
        .customer-initial:hover::after {
            content: attr(data-fullname);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            background-color: black;
            color: white;
            padding: 5px;
            border-radius: 5px;
            white-space: nowrap;
            z-index: 1000;
        }
    </style>
    <script>
        function navigateMonth(offset) {
            let month = <?php echo $currentMonth; ?>;
            let year = <?php echo $currentYear; ?>;
            month += offset;
            if (month < 0) {
                month = 11;
                year--;
            } else if (month > 11) {
                month = 0;
                year++;
            }
            window.location.href = `order_main.php?month=${month}&year=${year}`;
        }

        function populateInputFields(c_no, name, mobile, date) {
            document.getElementById('c_no').value = c_no;
            document.getElementById('name').value = name;
            document.getElementById('mobile').value = mobile;
            document.getElementById('date').value = date;
            
            // Highlight the selected customer row
            const rows = document.querySelectorAll('#customerTable tbody tr');
            rows.forEach(row => row.classList.remove('highlighted'));
            event.currentTarget.classList.add('highlighted');
        }

        function handleCalendarClick(day) {
            const c_no = document.getElementById('c_no').value.trim();
            const name = document.getElementById('name').value.trim();
            const mobile = document.getElementById('mobile').value.trim();
            const date = document.getElementById('date').value.trim();

            if (name === '' || mobile === '') {
                alert('Please first search the customer');
                return;
            }

            const selectedDate = `<?php echo $currentYear; ?>-${String(<?php echo $currentMonth + 1; ?>).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
            const url = `order_add.php?c_no=${encodeURIComponent(c_no)}&name=${encodeURIComponent(name)}&mobile=${encodeURIComponent(mobile)}&date=${encodeURIComponent(selectedDate)}`;
            window.location.href = url;
        }

        function toggleTableVisibility() {
            const table = document.getElementById('customerTable');
            const button = document.getElementById('showHideButton');
            if (table.classList.contains('hidden')) {
                table.classList.remove('hidden');
                button.textContent = 'Hide Results';
            } else {
                table.classList.add('hidden');
                button.textContent = 'Show Results';
            }
        }

      
function openModal(customerId) {
    // Construct the URL to fetch booking history
    const url = `fetch_booking_history.php?customer_id=${customerId}`;

    fetch(url)
        .then(response => response.text())
        .then(data => {
            // Insert the fetched data into the modal's table body
            document.querySelector('#bookingHistoryTableBody').innerHTML = data;
            // Show the modal
            document.getElementById("bookingHistoryModal").style.display = "block";
        })
        .catch(error => console.error('Error fetching booking history:', error));
}

function closeModal() {
    document.getElementById("bookingHistoryModal").style.display = "none";
}



        document.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            const showResults = urlParams.has('search') && urlParams.get('search') === 'true';
            const table = document.getElementById('customerTable');
            const button = document.getElementById('showHideButton');

            if (showResults) {
                table.classList.remove('hidden');
                button.textContent = 'Hide Results';
            } else {
                table.classList.add('hidden');
                button.textContent = 'Show Results';
            }
        });
    </script>
</head>
<body>
  
    <div class="container">
        <h1 style="color: #cd015b;">Booking Calendar </h1>
        <form method="POST" action="?search=true">
            <input type="hidden" id="c_no" name="c_no">
       <input type="text" id="name" name="name" placeholder="Enter customer Name" value="<?php echo $searchName; ?>">
            <input type="text" id="mobile" name="mobile" placeholder="Enter customer Mobile" value="<?php echo $searchMobile; ?>">
            <input type="date" id="date" name="date" value="<?php echo $searchDate; ?>">
            <button type="submit">Search</button>
        </form>
        <button id="showHideButton" class="show-hide-button" onclick="toggleTableVisibility()">Show Results</button>
        <div class="table-container">
            <table id="customerTable" class="<?php echo $customerFound ? '' : 'hidden'; ?>" border="1">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Mobile</th>
                        <th>Reg Date</th>
                        <th>History View</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($customerFound): ?>
                        <?php foreach ($customers as $customer): ?>
                            <tr onclick="populateInputFields('<?php echo addslashes($customer['c_no']); ?>', '<?php echo addslashes($customer['c_name']); ?>', '<?php echo addslashes($customer['c_mobile']); ?>', '<?php echo addslashes($customer['c_reg_date']); ?>')">
                                <td><?php echo htmlspecialchars($customer['c_name']); ?></td>
                                <td><?php echo htmlspecialchars($customer['c_mobile']); ?></td>
                                <td><?php echo htmlspecialchars($customer['c_reg_date']); ?> </td>
                                <td><button type="button" onclick="openModal('<?php echo $customer['c_no']; ?>')">Show Booking History</button></td>

                               
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3">No results found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="calendar-container">
            <div class="calendar-header">
                <span class="month-navigation"><a href="#" onclick="navigateMonth(-1)"> &lt; </a></span>
                <span class="month-year"><?php echo (new DateTime())->setDate($currentYear, $currentMonth + 1, 1)->format('F Y'); ?></span>
                <span class="month-navigation"><a href="#" onclick="navigateMonth(1)"> &gt; </a></span>
            </div>
            <div class="calendar-body">
            <?php
                $weekdays = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                foreach ($weekdays as $weekday) {
                    echo "<div class='calendar-day-title'>$weekday</div>";
                }

                $firstDay = new DateTime($currentYear . '-' . ($currentMonth + 1) . '-01');
                $firstDayOfWeek = $firstDay->format('w');
                $daysInMonth = $firstDay->format('t');

                $calendarDays = array_fill(0, $firstDayOfWeek, '');
                for ($day = 1; $day <= $daysInMonth; $day++) {
                    $calendarDays[] = $day;
                }
                $totalCells = ceil(count($calendarDays) / 7) * 7;
                $calendarDays = array_pad($calendarDays, $totalCells, '');

                foreach ($calendarDays as $key => $day) {
                    echo '<div class="calendar-day">';
                    if ($day !== '') {
                        $dateStr = sprintf('%04d-%02d-%02d', $currentYear, $currentMonth + 1, $day);
                        $orders = getOrderNamesByDate($dateStr, $conn);

                        echo "<a href=\"#\" onclick=\"handleCalendarClick($day)\">$day</a>";

                        if (!empty($orders)) {
                            echo "<div class='cusotmer_name' style='display: flex; flex-wrap: wrap; '>";
                            $displayedCustomers = 0;
                            foreach ($orders as $order) {
                                if ($displayedCustomers < 6) {  // Limit to 6 icons per day
                                    $initial = strtoupper(substr($order['c_name'], 0, 1));
                                    $color = generateRandomColor();
                                    // Add link to the initial, now displaying row-wise
                                    echo "<a href='order_view.php?o_no=" . urlencode($order['o_no']) . "' style='text-decoration: none;'>";
                                    echo "<span class='customer-initial' style='background-color: $color; border-radius: 50%; display: inline-block; width: 25px; height: 25px; text-align: center; line-height: 25px; color: white; font-weight: bold;' data-fullname='" . htmlspecialchars($order['c_name']) . "'>$initial</span>";
                                    echo "</a>";
                                    $displayedCustomers++;
                                }
                            }
                            echo "</div>";
                        }
                    }
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </div>

    <!-- Booking History Modal -->
    <div id="bookingHistoryModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <table class="modal-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Service</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody id="bookingHistoryTableBody">
                <!-- Booking data will be inserted here by JavaScript -->
            </tbody>
        </table>
    </div>
</div>
</body>
</html>



