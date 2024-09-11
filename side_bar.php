
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <title>Responsive Sidebar Menu</title>
    <link href="https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.01">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" href="./images/favicon" type="image/x-icon">
    <link rel="stylesheet" href="./css/side_bar.css">
</head>
<body data-role="<?php echo $_SESSION['role']; ?>"> <!-- Set this to 0 for admin and 1 for operator -->
    <div class="sidebar close">
        <div class="logo-details">
            <img src="./images/logo02.png" alt="" width="40px" height="40px" style="margin-top:50px; margin-left: 20px">
            <span class="logo_name" style="margin-top:65px; margin-left: 5px">SHINE NAIL&LASH</span><br><br>
            <span class="close-btn">&times;</span>
        </div>
        <ul class="nav-links">
            <li>
                <a href="main.php">
                    <i class='bx bx-grid-alt'></i>
                    <span class="link_name">Dashboard</span>
                </a>
                <ul class="sub-menu blank">
                    <li><a class="link_name" href="main.php">Dashboard</a></li>
                </ul>
            </li>
            <li>
                <div class="iocn-link">
                    <a href="customer_list.php">
                        <i class='bx bx-user'></i>
                        <span class="link_name">Customer</span>
                    </a>
                </div>
                <ul class="sub-menu">
                    <li><a class="link_name" href="customer_list.php">Customer</a></li>
                </ul>
            </li>
            <li>
                <div class="iocn-link">
                    <a href="#">
                        <i class='bx bx-receipt'></i>
                        <span class="link_name">Order</span>
                    </a>
                    <i class='bx bxs-chevron-down arrow'></i>
                </div>
                <ul class="sub-menu">
                    <li><a class="link_name" href="#">Order</a></li>
                    <li><a href="order_main.php">Booking Order</a></li>
                    <li><a href="booking_history.php">Booking History</a></li>
                </ul>
            </li>
            <li>
                <a href="feedback_main.php">
                    <i class='bx bx-message'></i>
                    <span class="link_name">Feedback</span>
                </a>
                <ul class="sub-menu blank">
                    <li><a class="link_name" href="feedback_main.php">Feedback</a></li>
                </ul>
            </li>
            <?php if ($_SESSION['role'] == '0'): // Admin specific menu items ?>
           
                <li>
                <div class="iocn-link">
                    <a href="#">
                        <i class='bx bx-wrench'></i>
                        <span class="link_name">Service</span>
                    </a>
                    <i class='bx bxs-chevron-down arrow'></i>
                </div>
                <ul class="sub-menu">
                    <li><a class="link_name" href="#">Service</a></li>
                    <li><a href="service_list.php">Service Manage</a></li>
                    <li><a href="promotion_list.php">Promotion</a></li>
                </ul>
            </li>
           
            <li>
                <a href="balance_sheet.php">
                    <i class='bx bx-calculator'></i>
                    <span class="link_name">Balance Sheet</span>
                </a>
                <ul class="sub-menu blank">
                    <li><a class="link_name" href="balance_sheet.php">Balance Sheet</a></li>
                </ul>
            </li>
            <li>
                <div class="iocn-link">
                    <a href="expense_manage.php">
                        <i class='bx bx-cog'></i>
                        <span class="link_name">Setting</span>
                    </a>
                    <i class='bx bxs-chevron-down arrow'></i>
                </div>
                <ul class="sub-menu">
                    <li><a class="link_name" href="#">Setting</a></li>
                    <li><a href="expense_manage.php">Expense Manage</a></li>
                    <li><a href="member_list.php">Member Manage</a></li>
                    <li><a href="currency_setting.php">Currency Setting</a></li>
                    <li><a href="feedback_statistic.php">Feedback List</a></li>
                </ul>
            </li>
            <?php endif; ?>
            <li>
                <div class="profile-details">
                    <div class="name-job">
                        <div class="profile_name">Welcome <?php echo $_SESSION['username']; ?></div>
                        <div class="job">
                            <?php 
                                if ($_SESSION['role'] == '1') {
                                    echo 'Operator';
                                } else {
                                    echo 'Admin';
                                }
                            ?>
                        </div>
                    </div>
                    <a href="logout.php"><i class='bx bx-log-out' type="logout"></i></a>
                </div>
            </li>
        </ul>
    </div>
    <section class="home-section">
        <div class="home-content">
            <i class='bx bx-menu'></i>
        </div>
    </section>
    <script>
    let arrow = document.querySelectorAll(".arrow");
for (let i = 0; i < arrow.length; i++) {
    arrow[i].addEventListener("click", (e) => {
        let arrowParent = e.target.parentElement.parentElement; // selecting main parent of arrow
        arrowParent.classList.toggle("showMenu");
    });
}
let sidebar = document.querySelector(".sidebar");
let sidebarBtn = document.querySelector(".bx-menu");
let closeBtn = document.querySelector(".close-btn");
let homeContent = document.querySelector(".home-content");

sidebarBtn.addEventListener("click", () => {
    sidebar.classList.toggle("close");
    toggleHomeIconVisibility();
});
closeBtn.addEventListener("click", () => {
    sidebar.classList.toggle("close");
    toggleHomeIconVisibility();
});

// Function to toggle the visibility of the home section icon
function toggleHomeIconVisibility() {
    if (!sidebar.classList.contains("close")) {
        homeContent.classList.add("hide-icon");
    } else {
        homeContent.classList.remove("hide-icon");
    }
}

// Function to open menus based on role
function openMenusBasedOnRole() {
    const role = document.body.getAttribute('data-role');
    if (role === '0') { // admin
        document.querySelectorAll('.nav-links .iocn-link').forEach(item => {
            item.parentElement.classList.add('showMenu');
        });
    } else if (role === '1') { // operator
        document.querySelectorAll('.nav-links > li').forEach((item, index) => {
            if ([0, 1, 2].includes(index)) {
                item.classList.add('showMenu');
            }
        });
    }
}

// Call the function to open menus based on role
openMenusBasedOnRole();

// Initial call to set home section icon visibility based on sidebar state
toggleHomeIconVisibility();


    </script>
</body>
</html>
