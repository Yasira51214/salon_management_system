<?php
include "common.php";
include "side_bar.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background-color: #f4f4f4;
            padding-top: 5px;
        }

        .container {
            max-width: 1200px;
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
            display: grid;
            grid-template-columns: 1fr 2fr 1fr;
            gap: 5px;
            grid-template-areas:
                "new-customer promotion today-reservation"
                "today-income today-expense total-balance"
                "calendar calendar calendar";
            margin-bottom: 10px;
        }
        .box {
            padding: 10px;
            border-radius: 8px;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
            text-align: left;
            color: white;
            margin-bottom:2px;
        }
        .new-customer {
            background-color: #4caf50;
            grid-area: new-customer;
            width: 100%;
            height: 180px;
        }

        .promotion {
            background-color: #f0ad4e;
            grid-area: promotion;
            width: 100%;
            height: 180px;
        }

        .today-reservation {
            background-color: #5bc0de;
            grid-area: today-reservation;
            width: 100%;
            height: 180px;
        }

        .today-income {
            background-color: #4caf50;
            grid-area: today-income;
            width: 100%;
            height: 50px;
        }

        .today-expense {
            background-color: #f0ad4e;
            grid-area: today-expense;
            width: 100%;
            height: 50px;
        }

        .total-balance {
            background-color: #777;
            grid-area: total-balance;
            width: 100%;
            height: 50px;
        }

        .calendar {
            background-color: white;
            color: black;
            padding: 10px;
            border-radius: 8px;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
            grid-area: calendar;
            height: 520px;
            /* overflow-y: auto; */
        }

        li {
            font-size: 17px;
        }

        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 5px;
            border-bottom: 1px solid #ccc;
            background-color: #2b6b5c;
            border-radius: 10px 10px 0 0;
            color: white;
        }

        .calendar-header button {
            background-color: #2b6b5c;
            border: none;
            color: white;
            font-size: 30px;
            cursor: pointer;
            padding: 5px 10px;
            border-radius: 4px;
        }

        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 3px;
            margin-top:5px;
        }

        .calendar-grid .day-name,
        .calendar-grid .day {
            padding: 10px;
            border-radius: 4px;
            text-align: center;
            font-size: 20px;
        }
        

        .calendar-grid .day-name {
            background-color: #2b6b5c;
            color: white;
            font-weight: bold;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }

        .calendar-grid .day {
            background-color: #e0e0e0;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .calendar-grid .day:hover {
            background-color: #f0f0f0;
        }

        .calendar-grid .day .ordersList a {
            font-size: 13px;
            text-decoration: none; 
            color:black;
      
        }
    
        .popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 200px;
            height: 200px;
            background-color: white;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
            border-radius: 8px;
            z-index: 1000;
        }

        .popup-content a {
            padding: 20px;
            text-align: left;
            font-size:15px;
            color:black;
            text-decoration: underline;
            text-decoration: none; 

        }

        .popup .close-popup {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 18px;
            cursor: pointer;
        }
        .preview {
        color: #888; /* Gray color for preview dates */
            font-size: 15px; /* Smaller font size */
        }
        @media (max-width: 768px) {
            .container {
                grid-template-columns: 1fr;
                grid-template-rows: repeat(8, auto);
                grid-template-areas:
                    "new-customer"
                    "promotion"
                    "today-reservation"
                    "today-income"
                    "today-expense"
                    "total-balance"
                    "calendar";
            }

            .new-customer { grid-area: new-customer; }
            .promotion { grid-area: promotion; }
            .today-reservation { grid-area: today-reservation; }
            .today-income { grid-area: today-income; }
            .today-expense { grid-area: today-expense; }
            .total-balance { grid-area: total-balance; }
            .calendar { grid-area: calendar; }
        }
        .customer-icon {
    display: inline-block;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    text-align: center;
    line-height: 30px;
    color: white;
    font-weight: bold;
    margin: 2px;
    cursor: pointer;
    position: relative;
}

.customer-icon:hover::after {
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
</head>
<body>

    <div class="container">
        <?php


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
        function fetchOrders($conn) {
            $sql = "SELECT o_date, customer.c_name, customer.c_no 
                    FROM `order`
                    JOIN customer ON `order`.o_c_no = customer.c_no
                    WHERE o_date >= CURDATE() - INTERVAL 1 YEAR";
            $result = $conn->query($sql);
            
            $orders = [];
            while ($row = $result->fetch_assoc()) {
                $date = $row['o_date'];
                $name = $row['c_name'];
                $c_no = $row['c_no'];  // Get customer number
                $orders[$date][] = ['c_name' => $name, 'c_no' => $c_no];  // Store both name and c_no
            }
            return $orders;
        }
        
        $orders = fetchOrders($conn);
        function fetchCustomerData($conn) {
            $today = date('Y-m-d');
            $sql = "SELECT c_name FROM customer WHERE DATE(c_reg_date) = '$today' ORDER BY c_reg_date DESC";
            $result = $conn->query($sql);
            
            $customerNames = [];
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $customerNames[] = $row['c_name'];
                }
            }
            $customerCount = count($customerNames);
            return [$customerNames, $customerCount];
        }
        
        list($customerNames, $customerCount) = fetchCustomerData($conn);
        ?>
        <div class="box new-customer">
            <h3>New Customers</h3>
            <?php
              echo "<ol>";
            $topFiveCustomers = array_slice($customerNames, 0, 4);
            foreach ($topFiveCustomers as $name) {
              
                echo "<li>$name</li>";
              
            }
            echo 'Customer Count:   '.$customerCount;
              echo "</ol>";
             
            ?>
            
        </div>
     

        <?php
        $sql = "SELECT p_name, p_s_date, p_e_date FROM promotion ORDER BY p_s_date DESC LIMIT 5";
        $result = $conn->query($sql);
          
        if ($result->num_rows > 0) {
            echo '<div class="box promotion">';
            echo '<h3>Promotion</h3><ol>';
            while ($row = $result->fetch_assoc()) {
                echo '<li><span class="promo-name">' . $row["p_name"] . '</span><span class="promo-dates">  ' . date("j M", strtotime($row["p_s_date"])) . ' To ' . date("j M", strtotime($row["p_e_date"])) . '</span></li>';
            }
            echo '</ol></div>';
        } else {
            echo '<div class="box promotion">No promotions available.</div>';
        }
        
        ?>
    
  <!-- today reservation -->
        <div class="box today-reservation">
            <h3>Reservation</h3>
            <?php
            $today = date('Y-m-d');
            if (isset($orders[$today])) {
                echo "<ol>";
                $orderList = $orders[$today];
                $topFiveOrders = array_slice($orderList, 0, 4);
                foreach ($topFiveOrders as $order) {
                    echo "<li>" . $order['c_name'] . "</li>";
                }
                echo "</ol>";
            } else {
                echo "<p>No reservations today.</p>";
            }
            ?>
        </div>

        <?php
        $today = date('Y-m-d');
        $query = "SELECT SUM(o_amount) AS total_income FROM `order` WHERE o_date = '$today'";
        $result = mysqli_query($conn, $query);
        $total_income = $result ? mysqli_fetch_assoc($result)['total_income'] : 0;
        ?>
        <div class="box today-income">
            <p>Today Income: <?php echo number_format($total_income); ?> &nbsp;<?= htmlspecialchars($currency) ?></p>
        </div>
        
        <?php
        $sql = "SELECT SUM(e_amount) AS total_expense FROM expense WHERE e_exp_date = '$today'";
        $result = $conn->query($sql);
        $total_expense = $result ? ($result->num_rows > 0 ? $result->fetch_assoc()['total_expense'] : 0) : 0;
        $conn->close();
        ?>
        <div class="box today-expense">
            <p>Today Expense: <?php echo number_format($total_expense); ?> &nbsp;<?= htmlspecialchars($currency) ?></p>
        </div>
        
        <?php
        $today_balance = $total_income - $total_expense;
        ?>
        <div class="box total-balance">
            <p>Total Balance: <?php echo number_format($today_balance); ?> &nbsp;<?= htmlspecialchars($currency) ?></p>
        </div>

        <!-- <div class="calendar-grid" id="calendar"></div> -->
        <div class="calendar">
        <div class="calendar-header">
            <button id="prevMonth">&lt;</button>
            <h2 id="calendarTitle"></h2>
            <button id="nextMonth">&gt;</button>
        </div>
        <div class="calendar-grid" id="calendar"></div>
    </div>

    <div id="popup" class="popup">
        <div class="popup-content">
            <span class="close-popup">&times;</span>
            <div id="popupDetails"></div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const calendar = document.getElementById('calendar');
    const calendarTitle = document.getElementById('calendarTitle');
    const prevMonth = document.getElementById('prevMonth');
    const nextMonth = document.getElementById('nextMonth');
    const popup = document.getElementById('popup');
    const popupDetails = document.getElementById('popupDetails');
    const closePopup = document.querySelector('.close-popup');

    let currentMonth = new Date().getMonth();
    let currentYear = new Date().getFullYear();
    const events = <?php echo json_encode($orders); ?>;

    function generateCalendar(month, year) {
        calendar.innerHTML = '';
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        const firstDay = new Date(year, month, 1).getDay();

        const prevMonthDays = new Date(year, month, 0).getDate();

        const dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        dayNames.forEach(dayName => {
            const dayNameElement = document.createElement('div');
            dayNameElement.classList.add('day-name');
            dayNameElement.textContent = dayName;
            calendar.appendChild(dayNameElement);
        });

        // Preview dates from the previous month
        for (let i = firstDay - 1; i >= 0; i--) {
            const previewDay = prevMonthDays - i;
            const previewDateElement = document.createElement('div');
            previewDateElement.classList.add('day', 'preview');
            previewDateElement.textContent = previewDay;
            calendar.appendChild(previewDateElement);
        }

        // Current month dates
        for (let day = 1; day <= daysInMonth; day++) {
            const dayElement = document.createElement('div');
            dayElement.classList.add('day');

            const dateKey = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
            dayElement.textContent = day;

            if (events[dateKey]) {
                const ordersList = document.createElement('div');
                ordersList.classList.add('ordersList');
                
                const names = events[dateKey];
                const firstTwoNames = names.slice(0, 2);
                const remainingNames = names.slice(2);

                firstTwoNames.forEach((order, index) => {
                    const link = document.createElement('a');
                    link.href = `cust_view_dashboard.php?c_id=${order.c_no}`;
                    link.textContent = order.c_name;
                    ordersList.appendChild(link);
                    if (index < firstTwoNames.length - 1) {
                        ordersList.appendChild(document.createElement('br'));
                    }
                });

                if (remainingNames.length > 0) {
                    const moreLink = document.createElement('a');
                    moreLink.href = '#';
                    moreLink.textContent = `+${remainingNames.length} more`;
                    moreLink.addEventListener('click', (e) => {
                        e.preventDefault();
                        popupDetails.innerHTML = '';
                        remainingNames.forEach((order, index) => {
                            const link = document.createElement('a');
                            link.href = `cust_view_dashboard.php?c_id=${order.c_no}`;
                            link.textContent = order.c_name;
                            popupDetails.appendChild(link);
                            if (index < remainingNames.length - 1) {
                                popupDetails.appendChild(document.createElement('br'));
                            }
                        });
                        popup.style.display = 'block';
                    });
                    ordersList.appendChild(document.createElement('br'));
                    ordersList.appendChild(moreLink);
                }

                dayElement.appendChild(ordersList);
            }

            calendar.appendChild(dayElement);
        }

        calendarTitle.textContent = `${new Date(year, month).toLocaleString('default', { month: 'long' })} ${year}`;
    }

    function changeMonth(offset) {
        currentMonth += offset;
        if (currentMonth < 0) {
            currentMonth = 11;
            currentYear -= 1;
        } else if (currentMonth > 11) {
            currentMonth = 0;
            currentYear += 1;
        }
        generateCalendar(currentMonth, currentYear);
    }

    closePopup.addEventListener('click', () => {
        popup.style.display = 'none';
    });

    prevMonth.addEventListener('click', () => changeMonth(-1));
    nextMonth.addEventListener('click', () => changeMonth(1));

    generateCalendar(currentMonth, currentYear);
});
document.addEventListener('DOMContentLoaded', () => {
    const calendar = document.getElementById('calendar');
    const calendarTitle = document.getElementById('calendarTitle');
    const prevMonth = document.getElementById('prevMonth');
    const nextMonth = document.getElementById('nextMonth');
    const popup = document.getElementById('popup');
    const popupDetails = document.getElementById('popupDetails');
    const closePopup = document.querySelector('.close-popup');

    let currentMonth = new Date().getMonth();
    let currentYear = new Date().getFullYear();
    const events = <?php echo json_encode($orders); ?>;

    function generateCalendar(month, year) {
        calendar.innerHTML = '';
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        const firstDay = new Date(year, month, 1).getDay();

        const prevMonthDays = new Date(year, month, 0).getDate();

        const dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        dayNames.forEach(dayName => {
            const dayNameElement = document.createElement('div');
            dayNameElement.classList.add('day-name');
            dayNameElement.textContent = dayName;
            calendar.appendChild(dayNameElement);
        });

        for (let i = firstDay - 1; i >= 0; i--) {
            const previewDay = prevMonthDays - i;
            const previewDateElement = document.createElement('div');
            previewDateElement.classList.add('day', 'preview');
            previewDateElement.textContent = previewDay;
            calendar.appendChild(previewDateElement);
        }

        for (let day = 1; day <= daysInMonth; day++) {
            const dayElement = document.createElement('div');
            dayElement.classList.add('day');

            const dateKey = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
            dayElement.textContent = day;

            if (events[dateKey]) {
                const ordersList = document.createElement('div');
                ordersList.classList.add('ordersList');

                events[dateKey].forEach(order => {
                    const initial = order.c_name.charAt(0).toUpperCase();
                    const color = generateRandomColor();
                    
                    const icon = document.createElement('span');
                    icon.classList.add('customer-icon');
                    icon.style.backgroundColor = color;
                    icon.setAttribute('data-fullname', order.c_name);
                    icon.textContent = initial;
                    icon.addEventListener('click', () => {
                        window.location.href = `cust_view_dashboard.php?c_id=${order.c_no}`;
                    });

                    ordersList.appendChild(icon);
                });

                dayElement.appendChild(ordersList);
            }

            calendar.appendChild(dayElement);
        }

        calendarTitle.textContent = `${new Date(year, month).toLocaleString('default', { month: 'long' })} ${year}`;
    }

    function changeMonth(offset) {
        currentMonth += offset;
        if (currentMonth < 0) {
            currentMonth = 11;
            currentYear -= 1;
        } else if (currentMonth > 11) {
            currentMonth = 0;
            currentYear += 1;
        }
        generateCalendar(currentMonth, currentYear);
    }

    closePopup.addEventListener('click', () => {
        popup.style.display = 'none';
    });

    prevMonth.addEventListener('click', () => changeMonth(-1));
    nextMonth.addEventListener('click', () => changeMonth(1));

    generateCalendar(currentMonth, currentYear);
});

function generateRandomColor() {
    const letters = '0123456789ABCDEF';
    let color = '#';
    for (let i = 0; i < 6; i++) {
        color += letters[Math.floor(Math.random() * 16)];
    }
    return color;
}
</script>

</body>
</html>