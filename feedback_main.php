<?php
include 'common.php';
include 'side_bar.php';

$customers = [];
$customerFound = false;
$searchName = '';
$searchMobile = '';
$searchDate = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = isset($_POST['customerName']) ? $_POST['customerName'] : '';
    $mobile = isset($_POST['mobile']) ? $_POST['mobile'] : '';
    $date = isset($_POST['orderDate']) ? $_POST['orderDate'] : '';

    // Initialize params and types
    $params = [];
    $types = '';

    // Query to search customers by name, mobile, or date
    $sql = "SELECT c_name, c_mobile FROM customer WHERE 1=1";
    if (!empty($name)) {
        $sql .= " AND c_name LIKE ?";
        $params[] = "%" . $name . "%";
        $types .= "s";
    }
    if (!empty($mobile)) {
        $sql .= " AND c_mobile LIKE ?";
        $params[] = "%" . $mobile . "%";
        $types .= "s";
    }
    if (!empty($date)) {
        $sql .= " AND c_reg_date LIKE ?";
        $params[] = "%" . $date . "%";
        $types .= "s";
    }

    $stmt = $conn->prepare($sql);
    if ($stmt) {
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $customers[] = $row;
            $customerFound = true;
        }

        $stmt->close();
    } else {
        die("Error preparing statement: " . $conn->error);
    }

    $searchName = htmlspecialchars($name);
    $searchMobile = htmlspecialchars($mobile);
    $searchDate = htmlspecialchars($date);
}

$currentMonth = isset($_GET['month']) ? (int)$_GET['month'] : (new DateTime())->format('n') - 1;
$currentYear = isset($_GET['year']) ? (int)$_GET['year'] : (new DateTime())->format('Y');

function getCustomerNamesByDate($date, $conn) {
    $feedbackCustomers = [];
    $orderCustomers = [];

    // SQL query to fetch customer names and numbers from feedback table
    $sqlFeedback = "SELECT DISTINCT c.c_name, c.c_no
                    FROM feedback f
                    JOIN customer c ON f.f_c_no = c.c_no
                    WHERE f.f_date = ?";
    
    // SQL query to fetch customer names and numbers from order table
    $sqlOrder = "SELECT DISTINCT c.c_name, c.c_no
                 FROM `order` o
                 JOIN customer c ON o.o_c_no = c.c_no
                 WHERE DATE(o.o_date) = ?";

    // Prepare and execute the SQL queries
    $stmtFeedback = $conn->prepare($sqlFeedback);
    if (!$stmtFeedback) {
        die("Error preparing feedback query: " . $conn->error);
    }
    $stmtFeedback->bind_param('s', $date);
    $stmtFeedback->execute();
    $resultFeedback = $stmtFeedback->get_result();

    while ($row = $resultFeedback->fetch_assoc()) {
        $feedbackCustomers[] = $row;
    }

    $stmtFeedback->close();

    $stmtOrder = $conn->prepare($sqlOrder);
    if (!$stmtOrder) {
        die("Error preparing order query: " . $conn->error);
    }
    $stmtOrder->bind_param('s', $date);
    $stmtOrder->execute();
    $resultOrder = $stmtOrder->get_result();

    while ($row = $resultOrder->fetch_assoc()) {
        $orderCustomers[] = $row;
    }

    $stmtOrder->close();
    return ['feedback' => $feedbackCustomers, 'order' => $orderCustomers];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Calendar</title>
    <link rel="stylesheet" href="./css/feedback_main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        /* Existing CSS rules... */

        .customer_name {
            font-size: 12px; /* Set the font size to 10px */
        }

        .customer_name a {
            display: flex;
            align-items: center;
        }

        .customer_name i {
            margin-right: 5px; /* Space between icon and text */
        }

        .highlighted {
            background-color: #8AB6A4;
            color: white;
            font-weight: bold;
        }

        @media (max-width: 768px) {
            .calendar-body {
                grid-template-columns: repeat(7, 2fr);
            }

            h1 {
                font-size: 24px;
            }

            .calendar-header .month-year {
                font-size: 16px;
            }

            table, thead, tbody, th, td, tr {
                display: block;
            }

            th {
                position: absolute;
                top: -9999px;
                left: -9999px;
            }

            tr {
                margin: 0 0 1rem 0;
            }

            td {
                border: none;
                position: relative;
                padding-left: 50%;
                text-align: right;
            }

            td:before {
                position: absolute;
                top: 50%;
                left: 6px;
                width: 45%;
                padding-right: 10px;
                white-space: nowrap;
                transform: translateY(-50%);
                text-align: left;
                font-weight: bold;
            }

            td:nth-of-type(1):before { content: "Customer"; }
            td:nth-of-type(2):before { content: "Mobile"; }

            form {
                flex-direction: column;
            }

            input[type="text"], input[type="date"] {
                width: 100%;
                margin-bottom: 10px;
            }

            button {
                width: 100%;
            }
        }

        @media (min-width: 768px) and (max-width: 1024px) {
            .calendar-body {
                grid-template-columns: repeat(7, 1fr);
            }
        }

        @media (min-width: 1024px) {
            .calendar-body {
                grid-template-columns: repeat(7, 1fr);
            }

            h1 {
                font-size: 32px;
            }

            .calendar-header .month-year {
                font-size: 18px;
            }
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
            window.location.href = `?month=${month}&year=${year}`;
        }

        function populateInputFields(name, mobile, row) {
            document.getElementById('customerName').value = name;
            document.getElementById('mobile').value = mobile;

            const rows = document.querySelectorAll('#customerTable tbody tr');
            rows.forEach(r => r.classList.remove('highlighted'));

            row.classList.add('highlighted');
        }

        function handleCalendarClick(day) {
            const name = document.getElementById('customerName').value.trim();
            const mobile = document.getElementById('mobile').value.trim();

            if (name === '' || mobile === '') {
                alert('Please first search the customer');
                return;
            }

            const selectedDate = `<?php echo $currentYear . '-' . ($currentMonth + 1); ?>-${day.toString().padStart(2, '0')}`;
            const url = `feedback_add.php?customerName=${encodeURIComponent(name)}&mobile=${encodeURIComponent(mobile)}&selectedDate=${encodeURIComponent(selectedDate)}`;
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
        <h1>Feedback</h1>
        <form method="POST" action="?search=true">
            <input type="text" id="customerName" name="customerName" placeholder="Name" value="<?php echo $searchName; ?>">
            <input type="text" id="mobile" name="mobile" placeholder="Mobile" value="<?php echo $searchMobile; ?>">
            <input type="date" id="orderDate" name="orderDate" value="<?php echo $searchDate; ?>">
            <button type="submit">Search</button>
        </form>
        <button id="showHideButton" class="show-hide-button" onclick="toggleTableVisibility()">Show Results</button>
        <div class="table-container">
            <table id="customerTable" class="<?php echo $customerFound ? '' : 'hidden'; ?>" border="1">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Mobile</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($customerFound): ?>
                        <?php foreach ($customers as $customer): ?>
                            <tr onclick="populateInputFields('<?php echo addslashes($customer['c_name']); ?>', '<?php echo addslashes($customer['c_mobile']); ?>', this)">
                                <td><?php echo htmlspecialchars($customer['c_name']); ?></td>
                                <td><?php echo htmlspecialchars($customer['c_mobile']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="2">No customers found.</td>
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
                        $customerNames = getCustomerNamesByDate($dateStr, $conn);

                        echo "<a href=\"#\" onclick=\"handleCalendarClick($day)\">$day</a>";

                        if (!empty($customerNames['feedback']) || !empty($customerNames['order'])) {
                            echo "<div class='customer_name'>";
                            foreach ($customerNames['feedback'] as $customer) {
                                $encodedDate = urlencode($dateStr);
                                $encodedName = urlencode($customer['c_name']);
                                $encodedCNo = urlencode($customer['c_no']);
                                echo "<a href='./feedback_view.php?date=$encodedDate&name=$encodedName'><i class='far fa-hand-point-right'></i> $customer[c_name]</a>";
                            }
                            foreach ($customerNames['order'] as $customer) {
                                $encodedDate = urlencode($dateStr);
                                $encodedName = urlencode($customer['c_name']);
                                $encodedCNo = urlencode($customer['c_no']);
                                echo "<a href='./feedback_add.php?date=$encodedDate&name=$encodedName&c_no=$encodedCNo'>$customer[c_name]</a>";
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
</body>
</html>
