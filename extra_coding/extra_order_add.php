<?php
// Database connection
require "db_connection.php";

// Fetch service items
$sql = "SELECT si_cat, si_service_name, si_price, si_image1, si_image2, si_image3, si_description FROM service_item";
$result = $conn->query($sql);

$services = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $services[] = $row;
    }
}

$conn->close();
?>

<?php
// Fetch data from URL parameters
$name = isset($_GET['name']) ? $_GET['name'] : 'Default Name';
$mobile = isset($_GET['mobile']) ? $_GET['mobile'] : 'Default Mobile';
$day = isset($_GET['day']) ? $_GET['day'] : '';
$month = isset($_GET['month']) ? $_GET['month'] : '';
$year = isset($_GET['year']) ? $_GET['year'] : '';
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Form</title>
    <link rel="stylesheet" type="text/css" href="./css/style_order_add.css">
</head>
<body>

<ul id="menu">
    <li><a href="#" onclick="showSection('all')">All</a></li>
    <li><a href="#" onclick="showSection('services')">Services</a></li>
    <li><a href="#" onclick="showSection('training')">Training</a></li>
    <li><a href="#" onclick="showSection('sales')">Sales</a></li>
</ul>
<div class="container">
    <div class="header">
        <div class="contact-info">
            <span style="font-weight:bold">Name:</span> <?php echo htmlspecialchars($name); ?> 
            <span style="font-weight:bold">Mobile:</span> <?php echo htmlspecialchars($mobile); ?>
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
                $price = htmlspecialchars($service['si_price']);
                $image1 = htmlspecialchars($service['si_image1']);
                $image2 = htmlspecialchars($service['si_image2']);
                $image3 = htmlspecialchars($service['si_image3']);
                $description = htmlspecialchars($service['si_description']);
                echo "<div class='service' data-price='$price' data-images='$image1,$image2,$image3' data-description='$description'>";
                echo "<input type='checkbox' class='service-checkbox'> $service_name";
                echo "<div class='quantity-control' style='display: none;'>";
                echo "<button class='decrement'>-</button>";
                echo "<input type='text' value='1' class='quantity'>";
                echo "<button class='increment'>+</button>";
                echo "</div>";
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
        <input type="text" placeholder="Type to here......" style="width: 50%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
    </div>

    <div class="footer">
        <div class="total">Total: <span id="total-amount">0</span> </div>
        <button type="button" class="submit" onclick="submitForm()">Submit</button>
        <button type="button" class="cancel" onclick="resetForm()">Cancel</button>
    </div>
</div>
<div id="popup" class="popup">
    <span id="popup-close" style="cursor: pointer; float: right; font-size: 20px;">&times;</span>
    <img id="popup-image" src="" alt="" style="width: 100%; max-height: 300px; object-fit: cover;">
    <p id="popup-description"></p>
    <button id="prev-btn" style="cursor: pointer;">&#8249; Prev</button>
    <button id="next-btn" style="cursor: pointer;">Next &#8250;</button>
</div>

<script>

document.addEventListener('DOMContentLoaded', function() {
    // Existing code...

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
            if (quantity > 0) {
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
    // Add your form submission logic here
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
