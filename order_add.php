<?php
include 'common.php';
include "side_bar.php"; 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);



$sql1 = "SELECT p_no, p_name, p_rate_price FROM promotion WHERE p_s_date <= NOW() AND p_e_date >= NOW() AND status = 1 LIMIT 1";
$result1 = $conn->query($sql1);

if ($result1 === false) {
    echo "Error in query: " . $conn->error;
    $promotionRow = null;
} else {
    $promotionRow = $result1->fetch_assoc() ?: null;
}

// Verify if the promotion data is valid and display it



// Fetch services associated with the promotion
$sql2 = "SELECT si_no, si.si_cat, si.si_service_name, si.si_price, si.si_promotion_price, si.si_image1, 
                si.si_image2, si.si_image3, si.si_description, ps.*
                FROM service_item AS si
                LEFT JOIN pro_service ps ON ps.pro_si_no = si.si_no AND ps.pro_p_no = " . ($promotionRow['p_no'] ?? 0);


$result2 = $conn->query($sql2);

if ($result2 === false) {
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

// Fetch data from URL parameters
$c_no = isset($_GET['c_no']) ? $_GET['c_no'] : '';
$name = isset($_GET['name']) ? $_GET['name'] : '';
$mobile = isset($_GET['mobile']) ? $_GET['mobile'] : '';
$date = isset($_GET['date']) ? $_GET['date'] : ''; // Correctly retrieve the date from the URL
$day = isset($_GET['day']) ? $_GET['day'] : '';


$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Form</title>
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
        .label{
            color: #cd015b;
        }
    </style>
</head>
<body>

<div class="container">
<h1 style="color: #cd015b; padding: 10px;text-align: center;"> Add Booking </h1>
    <ul class="menu" id="menu">
        <li class="menuli"><a href="#" onclick="showSection('all')">All</a></li>
        <li class="menuli"><a href="#" onclick="showSection('services')">Services</a></li>
        <li class="menuli"><a href="#" onclick="showSection('training')">Training</a></li>
        <li class="menuli"><a href="#" onclick="showSection('sales')">Sales</a></li>
    </ul>

    <?php 

    if($promotionRow && !empty($promotionRow['p_name'])) {
    ?>
    <div class="promotion">
        <h2>🎉 <?php echo htmlspecialchars($promotionRow['p_name']); ?> <?php echo htmlspecialchars($promotionRow['p_rate_price']); ?> Promotion 🎉</h2>
        <p>Don't miss out on this amazing offer!<p>
    </div>
    <?php 
    }
    ?>

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
        echo "<div class='topic-container' id='$cat-section'>";
        echo "<h2>$cat_name</h2>";
        echo "<div class='nail-services'>";
        foreach ($services as $service) {
            if ($service['si_cat'] == $cat) {
                $service_name = htmlspecialchars($service['si_service_name']);
                $price_origin = htmlspecialchars($service['si_price']);
                $price = $service['si_promotion_price'] && $service['si_promotion_price'] != $service['si_price']
                    ? htmlspecialchars($service['si_promotion_price'])
                    : $price_origin;
        
                $images = implode(',', array_filter([$service['si_image1'], $service['si_image2'], $service['si_image3']]));
                $description = htmlspecialchars($service['si_description']);
        
                echo "<div class='service' data-category='$cat' data-price='$price' data-images='$images' data-description='$description' data-si-no='{$service['si_no']}'>";
                echo "<input type='checkbox' class='service-checkbox'> $service_name";
                echo "<div class='quantity-control' style='display: none;'>";
                echo "<button class='decrement'>-</button>";
                echo "<input type='text' value='1' class='quantity'>";
                echo "<button class='increment'>+</button>";
                echo "<span class='price'>" .number_format( $price) . "</span>";  // Only this instance should display the price
                echo "</div>";
                
                if ($service['si_promotion_price'] && $service['si_price'] != $service['si_promotion_price']) {
                    echo "<span><strike>"  ."🎉". number_format($price_origin) . "</strike></span> ";
                }
                // echo "<span>" . number_format($price) . "</span>";
                echo "</div>";
            }
        }        

        echo "</div>";
        echo "</div>";
    }
    ?>

    <div class="section">
        <h2>Note</h2>
        <input type="text" placeholder="Type here..." id="note" style="padding: 10px; border: 1px solid #ddd; border-radius: 4px; width:100%; height:50px;">
    </div>
    <br><br>
    <form method="post" action="save_order.php">
    <input type="hidden" id="p_no" name="p_no" value="<?php echo htmlspecialchars($promotionRow['p_no'] ?? ''); ?>">
        <div class="footer">
            <div class="total">
                Total: <span id="total-amount"></span> &nbsp;<?= htmlspecialchars($currency) ?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <span>Payment</span>
                <select id="payment-method" name="payment-method">
                    <option value="none">None</option>
                    <option value="cash">Cash</option>
                    <option value="card">Card</option>
                    <option value="bank-transit">Bank Transit</option>
                </select>
            </div>
            <div class="buttons">
                <button type="button" class="submit" onclick="submitForm()">Save</button>
               <button type="button" class="cancel" onclick="resetForm()"><a href="./order_main.php">Cancel</a> </button>
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
                var price = parseInt(this.parentElement.dataset.price);
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
                var price = parseInt(this.parentElement.parentElement.dataset.price);
                quantityInput.value = quantity + 1;
                totalAmount += price;
                updateTotalAmount(totalAmount);
            });
        });

        decrementButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                var quantityInput = this.nextElementSibling;
                var quantity = parseInt(quantityInput.value);
                var price = parseInt(this.parentElement.parentElement.dataset.price);
                if (quantity > 1) {
                    quantityInput.value = quantity - 1;
                    totalAmount -= price;
                    updateTotalAmount(totalAmount);
                }
            });
        });
    });

    function updateTotalAmount(amount) {
        document.getElementById('total-amount').textContent = amount.toLocaleString() + ' .RS';
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
    let services = [];
    document.querySelectorAll('.service').forEach(function(service, index) {
        if (service.querySelector('.service-checkbox').checked) {
            let si_no = service.dataset.siNo; // Get the si_no value
            let category = service.dataset.category;
            let price = parseInt(service.dataset.price);
            let quantity = parseInt(service.querySelector('.quantity').value);
            if (quantity > 0) {
                services.push({
                    si_no: si_no,
                    category: category,
                    price: price,
                    quantity: quantity
                });
            }
        }
    });

    let formData = new FormData();
    formData.append('c_no', '<?php echo htmlspecialchars($c_no); ?>');
    formData.append('name', '<?php echo htmlspecialchars($name); ?>');
    formData.append('selectedDate', '<?php echo htmlspecialchars($date); ?>');
    formData.append('memo', document.getElementById('note').value);
    formData.append('p_no', document.getElementById('p_no').value);
    formData.append('payment-method', document.getElementById('payment-method').value);

    services.forEach((service, index) => {
        formData.append(`s_no${index + 1}`, service.si_no); // Append si_no instead of s_name
        formData.append(`s_cat${index + 1}`, service.category);
        formData.append(`s_qty${index + 1}`, service.quantity);
        formData.append(`s_price${index + 1}`, service.price);
    });

    fetch('save_order.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        alert("Order saved successfully");
        window.location.href = "order_main.php";
    })
    .catch(error => {
        console.error('Error:', error);
    });
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
</div>
</body>
</html>
