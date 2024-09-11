<?php
  include 'common.php';
 ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Balance Sheet</title>
    <link rel="stylesheet" type="text/css" href="./css/balance_sheet.css">
    <style>
        /* Responsive Design */
        @media (max-width: 768px) {
            .calendar-body {
                grid-template-columns: repeat(7, 1fr); /* 1fr ensures all days fit in a single row */
                overflow-x: auto;
            }

            .calendar-header {
                flex-direction: column;
                align-items: center;
            }

            .calendar-header .prev-month,
            .calendar-header .next-month {
                font-size: 18px;
                padding: 5px;
            }

            .calendar-day-title {
                font-size: 12px;
                padding: 8px;
                font-weight: bold;
            }

            /* .day {
                font-size: 14px;
                padding: 10px;
            } */
        }

        @media (min-width: 769px) and (max-width: 1024px) {
            .calendar-body {
                grid-template-columns: repeat(7, 1fr); /* Adjust for tablet size */
                gap: 1px; /* Slightly larger gap for better spacing */
            }

            .calendar-header {
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
            }

            .calendar-header .month-year {
                font-size: 20px;
            }

            /* .day {
                font-size: 16px;
                padding: 12px;
            } */
        }

        @media (min-width: 1025px) {
            .container {
                width: 80%;
            }

            .calendar-body {
                grid-template-columns: repeat(7, 1fr);
            }

            .calendar-header .month-year {
                font-size: 24px;
            }

            .calendar-day-title {
                font-size: 16px;
                padding: 12px;
                text-align:center;
                font-weight: bold;
            }

            /* .day {
                font-size: 18px;
                padding: 15px;
            } */
        }
    </style>
</head>
<body>
    <?php include 'side_bar.php'; ?>    

    <div class="container">
        <h1>Balance Sheet</h1>
        <br>
        <div class="calendar-container">
            <div class="calendar-header">
                <a href="#" class="prev-month"><</a>
                <span class="month-year">July 2024</span>
                <a href="#" class="next-month">></a>
            </div>
            <div class="calendar-body">
            <script>
            document.addEventListener('DOMContentLoaded', function () {
                const calendarBody = document.querySelector('.calendar-body');
                const monthYear = document.querySelector('.month-year');
                const prevMonth = document.querySelector('.prev-month');
                const nextMonth = document.querySelector('.next-month');

                let currentDate = new Date();

                function redirectToExpenseModify(dateStr) {
                    window.location.href = `./balance_view.php?date=${dateStr}`;
                }

                function renderCalendar(date) {
                    calendarBody.innerHTML = ''; // Clear existing calendar days
                    const year = date.getFullYear();
                    const month = date.getMonth();

                    monthYear.textContent = date.toLocaleString('default', { month: 'long', year: 'numeric' });

                    const firstDayIndex = new Date(year, month, 1).getDay();
                    const lastDay = new Date(year, month + 1, 0).getDate();
                    const prevLastDay = new Date(year, month, 0).getDate();

                    const dayTitles = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                    dayTitles.forEach(day => {
                        calendarBody.innerHTML += `<div class="calendar-day-title">${day}</div>`;
                    });

                    for (let i = firstDayIndex; i > 0; i--) {
                        const day = document.createElement('div');
                        day.classList.add('day');
                        day.textContent = prevLastDay - i + 1;
                        calendarBody.appendChild(day);
                    }

                    for (let i = 1; i <= lastDay; i++) {
                        const day = document.createElement('div');
                        day.classList.add('day');
                        const dateStr = `${year}-${month + 1}-${i}`;
                        day.innerHTML = `<div>${i}</div>`;
                        day.dataset.dateStr = dateStr;
                        day.addEventListener('click', function() {
                            redirectToExpenseModify(dateStr);
                        });
                        calendarBody.appendChild(day);
                    }

                    const totalDays = firstDayIndex + lastDay;
                    const nextDays = (7 - totalDays % 7) % 7;
                    for (let i = 1; i <= nextDays; i++) {
                        const day = document.createElement('div');
                        day.classList.add('day');
                        day.textContent = i;
                        calendarBody.appendChild(day);
                    }

                    fetchOrderData(year, month + 1);
                }

                function fetchOrderData(year, month) {
                    fetch(`get_orders.php?year=${year}&month=${month}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.error) {
                                console.error('Error fetching order data:', data.error);
                                return;
                            }
                            const orderData = data.orders;
                            const expenseData = data.expenses;

                            // Function to calculate and display the balance
                            function calculateAndDisplayBalance(orderData, expenseData, year, month) {
                                const balanceData = {};

                                // Calculate the balance for each day
                                for (const day in orderData) {
                                    balanceData[day] = (orderData[day] || 0) - (expenseData[day] || 0);
                                }

                                for (const day in expenseData) {
                                    if (!balanceData.hasOwnProperty(day)) {
                                        balanceData[day] = -(expenseData[day] || 0);
                                    }
                                }

                                // Create and append the balance elements
                                for (const day in balanceData) {
                                    const balanceElement = document.createElement('div');
                                    balanceElement.classList.add('balance');
                                    balanceElement.innerHTML = `Balance:&nbsp;${balanceData[day] || 0}`;
                                    document.querySelector(`[data-date-str="${year}-${month}-${day}"]`).appendChild(balanceElement);
                                }
                            }

                            for (const day in orderData) {
                                const incomeElement = document.createElement('div');
                                incomeElement.classList.add('income');
                                incomeElement.innerHTML = `Income:&nbsp;${orderData[day] || 0}`;
                                if (orderData[day]) {
                                    document.querySelector(`[data-date-str="${year}-${month}-${day}"]`).appendChild(incomeElement);
                                }
                            }
                            for (const day in expenseData) {
                                const expenseElement = document.createElement('div');
                                expenseElement.classList.add('expense');
                                expenseElement.innerHTML = `Expense:&nbsp;${expenseData[day] || 0}`;
                                if (expenseData[day]) {
                                    document.querySelector(`[data-date-str="${year}-${month}-${day}"]`).appendChild(expenseElement);
                                }
                            }

                            // Calculate and display balance
                            calculateAndDisplayBalance(orderData, expenseData, year, month);
                        })
                        .catch(error => console.error('Error fetching order data:', error));
                }

                renderCalendar(currentDate);

                prevMonth.addEventListener('click', function () {
                    currentDate.setMonth(currentDate.getMonth() - 1);
                    renderCalendar(currentDate);
                });

                nextMonth.addEventListener('click', function () {
                    currentDate.setMonth(currentDate.getMonth() + 1);
                    renderCalendar(currentDate);
                });
            });
        </script>
            </div>
        </div>

       
    </div>
</body>
</html>
