<?php
include 'common.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Initialize totalAmount
$totalAmount = 0;

// Get c_no from the GET request
$o_no = $_GET['o_no'] ?? '';
if (empty($o_no)) {
    die("Customer number is required.");
}

// Fetch customer details and order details
$sql = "SELECT c.c_name, c.c_mobile, o.o_date, o.o_amount, o.o_memo, o_pro_no, o_pymt_method FROM `customer` c
        JOIN `order` o ON c.c_no = o.o_c_no
        WHERE o.o_no = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Error preparing statement: " . $conn->error);
}
$stmt->bind_param("s", $o_no);
$stmt->execute();
$result = $stmt->get_result();
if (!$result) {
    die("Error executing query: " . $stmt->error);
}
if ($result->num_rows == 0) {
    die("No customer found with the given number.");
}
$customer = $result->fetch_assoc();
$pro_no = $customer['o_pro_no'];
$name = $customer['c_name'];
$mobile = $customer['c_mobile'];
$memo = $customer['o_memo'];
$method = $customer['o_pymt_method'];


$stmt->close();

// Fetch previously selected services
$sql = "SELECT os.s_cat, os.s_si_no, os.s_price, os.s_qty, si.si_no, si.si_service_name, si.si_cat 
        FROM `orderservice` os
        JOIN `service_item` si ON os.s_si_no = si.si_no
        WHERE os.s_o_no = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Error preparing statement: " . $conn->error);
}
$stmt->bind_param("i", $o_no);
$stmt->execute();
$result = $stmt->get_result();
$selectedServices = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $selectedServices[$row['si_no']] = $row;

        // Calculate total amount
        $totalAmount += $row['s_price'] * $row['s_qty'];
    }
}
$stmt->close();
// Fetch promotion data
$sql1 = "SELECT p_no, p_name, p_rate_price FROM promotion WHERE p_no = '".$pro_no."'";

  // p_s_date <= NOW() AND p_e_date >= NOW() AND status = 1 LIMIT 1;
$result1 = $conn->query($sql1);
if (!$result1) {
    echo "Error in query: " . $conn->error;
    $promotionRow = null;
} else {
    $promotionRow = $result1->fetch_assoc() ?: null;
}

// Fetch services associated with the promotion
$sql2 = "SELECT si_no, si.si_cat, si.si_service_name, si.si_price, si.si_promotion_price, si.si_image1, 
                si.si_image2, si.si_image3, si.si_description
                FROM service_item AS si
                LEFT JOIN pro_service ps ON ps.pro_si_no = si.si_no AND ps.pro_p_no = " . ($promotionRow['p_no'] ?? 0);
$result2 = $conn->query($sql2);
if (!$result2) {
    echo "Error in query: " . $conn->error;
    $services = [];
} else {
    $services = $result2->fetch_all(MYSQLI_ASSOC);
}

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

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modify Order</title>
    <link rel="stylesheet" type="text/css" href="./css/order_add.css">
    <style>
        .promotion {
            background-color: #f9f9f9;
            border: 2px solid #ffc107;
            border-radius: 10px;
            padding: 10px;
            text-align: center;
            margin: 10px auto;
            width: 80%;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }

        .promotion h2 {
            font-size: 2em;
            color: #ff5722;
            margin-bottom: 10px;
        }

        .promotion p {
            font-size: 1.2em;
            color: #333;
        }
    </style>
</head>
<body>
<?php include "side_bar.php"; ?><br>

<div class="container">
    <input type="hidden" name="c_no" value="<?php ($promotionRow['p_no'] ?? 0) ?>">

    <ul class="menu" id="menu">
        <li class="menuli"><a href="#" onclick="showSection('all')">All</a></li>
        <li class="menuli"><a href="#" onclick="showSection('services')">Services</a></li>
        <li class="menuli"><a href="#" onclick="showSection('training')">Training</a></li>
        <li class="menuli"><a href="#" onclick="showSection('sales')">Sales</a></li>
    </ul>

    <?php if($promotionRow && !empty($promotionRow['p_name'])) { ?>
    <div class="promotion">
        <h2>🎉 <?php echo htmlspecialchars($promotionRow['p_name']); ?> <?php echo htmlspecialchars($promotionRow['p_rate_price']); ?> Promotion 🎉</h2>
        <p>Don't miss out on this amazing offer!</p>
    </div>
    <?php } ?>

    <div class="header">
        <div class="contact-info">
            <span class="label">Name:</span> <span class="info"><?php echo htmlspecialchars($name); ?></span>
            <span class="label">Mobile:</span> <span class="info"><?php echo htmlspecialchars($mobile); ?></span>
        </div>
    </div>

    <?php
    $categories = [
        'N' => 'Nail',
        'P' => 'Pedicure',
        'E' => 'Eyelashes',
        'M' => 'Massage',
        'T' => 'Training',
        'S' => 'Sales'
    ];

    foreach ($categories as $cat => $cat_name) {
        echo "<div class='topic-container' id='{$cat}-section'>";
        echo "<h2>$cat_name</h2>";
        echo "<div class='nail-services'>";

        foreach ($services as $service) {
            if ($service['si_cat'] == $cat) {
                $service_id = htmlspecialchars($service['si_no']);
                $service_name = htmlspecialchars($service['si_service_name']);
                $price_origin = htmlspecialchars($service['si_price']);
                $price = $service['si_promotion_price'] && $service['si_promotion_price'] != $service['si_price']
                    ? htmlspecialchars($service['si_promotion_price'])
                    : $price_origin;
        
                $images = implode(',', array_filter([$service['si_image1'], $service['si_image2'], $service['si_image3']]));
                $description = htmlspecialchars($service['si_description']);
                $checked = isset($selectedServices[$service_id]) ? 'checked' : '';
                $quantity = isset($selectedServices[$service_id]) ? $selectedServices[$service_id]['s_qty'] : 1;
        
                echo "<div class='service' data-category='$cat' data-price='$price' data-images='$images' data-description='$description'>";
                echo "<input type='checkbox' class='service-checkbox' name='services[]' value='$service_id' $checked> $service_name";
                echo "<div class='quantity-control' style='display: " . ($checked ? 'flex' : 'none') . ";'>";
                echo "<button class='decrement'>-</button>";
                echo "<input type='text' value='$quantity' class='quantity' name='quantities[$service_name]'>";
                echo "<button class='increment'>+</button>";
                echo "</div>";
                if ($service['si_promotion_price'] && $service['si_price'] != $service['si_promotion_price']) {
                    echo "<span><strike>" . number_format($price_origin) . "</strike></span> 🎉  ";
                }
                echo "<span>" . number_format($price) . "</span>";
                echo "</div>";
            }
        }
        echo "</div>";
        echo "</div>";
    }
    ?>

    <div class="section">
        <h2>Note</h2>
        <input type="text" placeholder="Type here..." id="note" name="note" value="<?php echo htmlspecialchars($memo); ?>" style="padding: 10px; border: 1px solid #ddd; border-radius: 4px; width:100%; height:50px;">
    </div>
    <br><br>
    <form method="post" action="update_order.php">
        <input type="hidden" name="o_no" value="<?php echo htmlspecialchars($o_no); ?>">
        <div class="footer">
            <div class="total">
                Total: <span id="total-amount" ><?php echo htmlspecialchars($totalAmount); ?></span>  &nbsp;<?= htmlspecialchars($currency) ?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <span>Payment</span>
                <select id="payment-method" name="payment-method">
                <option value="none" <?php echo $method == 'none' ? 'selected' : ''; ?>>None</option>
                <option value="cash" <?php echo $method == 'cash' ? 'selected' : ''; ?>>Cash</option>
                <option value="card" <?php echo $method == 'card' ? 'selected' : ''; ?>>Card</option>
                <option value="bank-transit" <?php echo $method == 'bank-transit' ? 'selected' : ''; ?>>Bank Transit</option>
            </select>
            </div>
            <div class="buttons">
                <button type="submit" class="submit">Save</button>
                <button type="button" class="cancel" onclick="window.location.href='./order_main.php'">Cancel</button>
            </div>
        </div>
    </form>

    <div id="popup" class="popup">
        <span id="popup-close">&times;</span>
        <img id="popup-image" src="" alt="">
        <p id="popup-description"></p>
        <button id="prev-btn">&#8249; Prev</button>
        <button id="next-btn">Next &#8250;</button>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var popup = document.getElementById('popup');
            var popupClose = document.getElementById('popup-close');
            var popupImage = document.getElementById('popup-image');
            var popupDescription = document.getElementById('popup-description');
            var prevBtn = document.getElementById('prev-btn');
            var nextBtn = document.getElementById('next-btn');
            var currentImageIndex = 0;
            var images = [];

            document.querySelectorAll('.service').forEach(function(service) {
                service.addEventListener('click', function() {
                    images = this.dataset.images.split(',');
                    currentImageIndex = 0;
                    var description = this.dataset.description;

                    popupImage.src = images[currentImageIndex];
                    popupDescription.textContent = description;

                    popup.style.display = 'block';
                });
            });

            popupClose.addEventListener('click', function() {
                popup.style.display = 'none';
            });

            nextBtn.addEventListener('click', function() {
                currentImageIndex = (currentImageIndex + 1) % images.length;
                popupImage.src = images[currentImageIndex];
            });

            prevBtn.addEventListener('click', function() {
                currentImageIndex = (currentImageIndex - 1 + images.length) % images.length;
                popupImage.src = images[currentImageIndex];
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            var checkboxes = document.querySelectorAll('.service-checkbox');
            var totalAmount = 0;

            checkboxes.forEach(function(checkbox) {
                checkbox.addEventListener('change', function() {
                    var quantityControl = this.nextElementSibling;
                    var price = parseFloat(this.parentElement.dataset.price);
                    var quantity = parseInt(quantityControl.querySelector('.quantity').value);

                    if (this.checked) {
                        quantityControl.style.display = 'flex';
                        totalAmount += price * quantity;
                    } else {
                        quantityControl.style.display = 'none';
                        totalAmount -= price * quantity;
                    }
                    updateTotalAmount(totalAmount);
                });
            });

            var incrementButtons = document.querySelectorAll('.increment');
            var decrementButtons = document.querySelectorAll('.decrement');

            incrementButtons.forEach(function(button) {
                button.addEventListener('click', function() {
                    var quantityInput = this.previousElementSibling;
                    var quantity = parseInt(quantityInput.value);
                    var price = parseFloat(this.parentElement.parentElement.dataset.price);
                    quantityInput.value = quantity + 1;
                    totalAmount += price;
                    updateTotalAmount(totalAmount);
                });
            });

            decrementButtons.forEach(function(button) {
                button.addEventListener('click', function() {
                    var quantityInput = this.nextElementSibling;
                    var quantity = parseInt(quantityInput.value);
                    var price = parseFloat(this.parentElement.parentElement.dataset.price);
                    if (quantity > 1) {
                        quantityInput.value = quantity - 1;
                        totalAmount -= price;
                        updateTotalAmount(totalAmount);
                    }
                });
            });
        });

        function updateTotalAmount(amount) {
            document.getElementById('total-amount').textContent = amount.toLocaleString();
        }

        function resetForm() {
            document.querySelectorAll('.service-checkbox').forEach(function(checkbox) {
                checkbox.checked = false;
                checkbox.nextElementSibling.style.display = 'none';
            });
            document.querySelectorAll('.quantity').forEach(function(input) {
                input.value = 1;
            });
            updateTotalAmount(0);
        }

        function submitForm() {
            alert('Form submitted');
        }

        function showSection(section) {
            var allSections = document.querySelectorAll('.topic-container');
            var servicesSections = document.querySelectorAll('#N-section, #P-section, #E-section, #M-section');
            var trainingSection = document.querySelector('#T-section');
            var salesSection = document.querySelector('#S-section');

            switch (section) {
                case 'all':
                    allSections.forEach(function(container) {
                        container.style.display = 'block';
                    });
                    break;
                case 'services':
                    allSections.forEach(function(container) {
                        container.style.display = 'none';
                    });
                    servicesSections.forEach(function(container) {
                        container.style.display = 'block';
                    });
                    break;
                case 'training':
                    allSections.forEach(function(container) {
                        container.style.display = 'none';
                    });
                    trainingSection.style.display = 'block';
                    break;
                case 'sales':
                    allSections.forEach(function(container) {
                        container.style.display = 'none';
                    });
                    salesSection.style.display = 'block';
                    break;
                default:
                    break;
            }
        }
    </script>
</body>
</html>
