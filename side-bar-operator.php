<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <title>Responsive Sidebar Menu</title>
    <link href="https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.01">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" href="./images/favicon" type="image/x-icon">
    <link rel="stylesheet" href="./css/side-bar.css">
</head>
<body data-role="<?php echo $_SESSION['role']; ?>"> <!-- Set this to 0 for admin and 1 for operator -->
    <div class="sidebar close">
        <div class="logo-details">
            <img src="./images/picture1" alt="" width="50px" height="50px">
            <span class="logo_name">SHINE NAIL LASH</span>
            <span class="close-btn">&times;</span>
        </div>
        <div class="logo-details">
            <span class="logo_name"><span style="color: #FF337A;">Salon HOKMA System</span></span>
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
                    <a href="#">
                        <i class='bx bx-user'></i>
                        <span class="link_name">Customer</span>
                    </a>
                    <i class='bx bxs-chevron-down arrow'></i>
                </div>
                <ul class="sub-menu">
                    <li><a class="link_name" href="#">Customer</a></li>
                    <li><a href="register.php">Customer New</a></li>
                    <li><a href="customer-list.php">Customer List</a></li>
                </ul>
            </li>
            <li>
                <a href="order_main.php">
                    <i class='bx bx-receipt'></i>
                    <span class="link_name">Order</span>
                </a>
                <ul class="sub-menu blank">
                    <li><a class="link_name" href="order_main.php">Order</a></li>
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
                let arrowParent = e.target.parentElement.parentElement; //selecting main parent of arrow
                arrowParent.classList.toggle("showMenu");
            });
        }
        let sidebar = document.querySelector(".sidebar");
        let sidebarBtn = document.querySelector(".bx-menu");
        let closeBtn = document.querySelector(".close-btn");
        sidebarBtn.addEventListener("click", () => {
            sidebar.classList.toggle("close");
        });
        closeBtn.addEventListener("click", () => {
            sidebar.classList.toggle("close");
        });

        // Function to open menus based on role
        function openMenusBasedOnRole() {
            const role = document.body.getAttribute('data-role');
            if (role === '0') { // admin
                document.querySelectorAll('.nav-links .iocn-link').forEach(item => {
                    item.parentElement.classList.add('showMenu');
                });
            } else if (role === '1') { // operator
                document.querySelectorAll('.nav-links > li').forEach((item, index) => {
                    if ([0, 1, 2, 5].includes(index)) {
                        item.classList.add('showMenu');
                    }
                });
            }
        }

        // Call the function to open menus based on role
        openMenusBasedOnRole();
    </script>
</body>
</html>
