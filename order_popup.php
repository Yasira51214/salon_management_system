<?php
include 'common.php';
?>
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Fetch service items
$sql = "SELECT si_cat, si_service_name, si_price, si_image1, si_image2, si_image3, si_description FROM service_item";
$result = $conn->query($sql);

$services = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $services[] = $row;
    }
}  

$conn->close();
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


<div class="container">
 
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
                echo "<div class='service' data-category='$cat' data-price='$price' data-images='$image1,$image2,$image3' data-description='$description'>";
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
  

    </script>
</body>
</html>
