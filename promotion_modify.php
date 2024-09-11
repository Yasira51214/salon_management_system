<?php
include 'common.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Fetch service items, including si_no
$sql = "SELECT si_no, si_cat, si_service_name, si_price FROM service_item";
$result = $conn->query($sql);

$services = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $services[$row['si_cat']][] = $row;
    }
}

// Retrieve the promotion ID from the query string
$promotion_id = $_GET['p_no'] ?? '';
$promotion = null;

if ($promotion_id) {
    // Fetch existing promotion data
    $stmt = $conn->prepare("SELECT * FROM promotion WHERE p_no = ?");
    $stmt->bind_param("i", $promotion_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $promotion = $result->fetch_assoc();
    $stmt->close();

    if (!$promotion) {
        die('Error: Promotion not found.');
    }

    // Fetch existing services for this promotion
    $stmt = $conn->prepare("SELECT pro_s_cat, pro_si_no, pro_s_price FROM pro_service WHERE pro_p_no = ?");
    $stmt->bind_param("i", $promotion_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $existing_services = [];
    while ($row = $result->fetch_assoc()) {
        $existing_services[$row['pro_s_cat']][$row['pro_si_no']] = $row['pro_s_price'];
    }
    $stmt->close();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $p_name = $_POST['p_name'] ?? '';
    $p_s_date = $_POST['p_s_date'] ?? '';
    $p_e_date = $_POST['p_e_date'] ?? '';
    $promotionType = $_POST['promotionType'] ?? '';
    $selected_services = $_POST['selected_services'] ?? [];
    $pro_s_prices = $_POST['pro_s_price'] ?? [];

    // Depending on promotion type, select the right price input
    $p_rate_price = ($promotionType === 'Rate') ? ($_POST['rateInput'] ?? '') : '';

    // Validate required fields based on promotion type
    if (empty($p_name) || empty($p_s_date) || empty($p_e_date) || empty($promotionType)) {
        die('Error: Please fill in all required fields.');
    }

    if ($promotionType === "Rate" && empty($p_rate_price)) {
        die('Error: Please fill in the rate price for Rate promotion type.');
    }

    if ($promotionType === "Price" && empty($selected_services)) {
        die('Error: Please select at least one service for Price promotion type.');
    }

    // Update promotion data
    $stmt = $conn->prepare("UPDATE promotion SET p_name = ?, p_s_date = ?, p_e_date = ?, p_rate_price = ? WHERE p_no = ?");
    if (!$stmt) {
        die('Prepare failed: ' . $conn->error);
    }
    $stmt->bind_param("sssii", $p_name, $p_s_date, $p_e_date, $p_rate_price, $promotion_id);
    if (!$stmt->execute()) {
        die('Execute failed: ' . $stmt->error);
    }
    $stmt->close();

    // Remove existing services
    $stmt = $conn->prepare("DELETE FROM pro_service WHERE pro_p_no = ?");
    if (!$stmt) {
        die('Prepare failed: ' . $conn->error);
    }
    $stmt->bind_param("i", $promotion_id);
    if (!$stmt->execute()) {
        die('Execute failed: ' . $stmt->error);
    }
    $stmt->close();

    // Insert updated services including si_no
    $stmt = $conn->prepare("INSERT INTO pro_service (pro_p_no, pro_s_cat, pro_si_no, pro_s_price) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        die('Prepare failed: ' . $conn->error);
    }

    foreach ($selected_services as $service) {
        list($cat, $name, $price) = explode('|', $service);

        // Determine promotion price based on the selected option
        if ($promotionType === 'Rate') {
            $promotion_price = $price;  // Use original price for Rate option
        } else {
            // For Price option, use entered price or original if not entered
            $promotion_price = $pro_s_prices[$cat . "|" . $name] ?? $price;
        }

        // Fetch the si_no from the services array based on cat and name
        $si_no = null;
        foreach ($services[$cat] as $item) {
            if ($item['si_service_name'] === $name) {
                $si_no = $item['si_no'];
                break;
            }
        }

        // If si_no is not found, generate a placeholder or handle as needed
        if (!$si_no) {
            $si_no = 'UNKNOWN';
        }

        $stmt->bind_param("isss", $promotion_id, $cat, $si_no, $promotion_price);
        if (!$stmt->execute()) {
            die('Execute failed: ' . $stmt->error);
        }
    }

    $stmt->close();

    // Update the promotion table with the actual number of services
    $stmt = $conn->prepare("UPDATE promotion SET p_s_items = ? WHERE p_no = ?");
    if (!$stmt) {
        die('Prepare failed: ' . $conn->error);
    }
    $stmt->bind_param("ii", count($selected_services), $promotion_id);
    if (!$stmt->execute()) {
        die('Execute failed: ' . $stmt->error);
    }
    $stmt->close();

    $conn->close();

    header("Location: promotion_list.php"); // Redirect after success
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Promotion Modify</title>
    <link rel="stylesheet" type="text/css" href="./css/promotion_add.css">
    <script>
        function togglePromotionInput() {
            var promotionType = document.getElementById("promotionType").value;
            var rateInput = document.getElementById("rateInput");
        
            // Hide both inputs initially
            rateInput.style.display = "none";
        
            // Show the correct input based on the promotion type selected
            if (promotionType === "Rate") {
                rateInput.style.display = "inline-block";
            }
        }

        function toggleServiceInput(serviceId) {
            const inputField = document.getElementById('input-' + serviceId);
            const promotionType = document.getElementById('promotionType').value;

            if (promotionType === 'Price') {
                inputField.style.display = inputField.style.display === 'none' || inputField.style.display === '' ? 'inline-block' : 'none';
            }
        }

        // Initialize the promotion input visibility on page load
        document.addEventListener('DOMContentLoaded', function() {
            togglePromotionInput();
        });
    </script>
</head>
<body>
<?php include "side_bar.php"; ?><br>

<div class="container">
    <h1>Promotion Modify</h1>
    <form method="POST" action="">
    <table class="customer-table">
        <tr>
            <th>Promotion Name</th>
            <td colspan="2"><input type="text" name="p_name" value="<?php echo htmlspecialchars($promotion['p_name'] ?? ''); ?>" required></td>
        </tr>
        <tr>
            <th>Start Date</th>
            <td colspan="2"><input type="date" name="p_s_date" value="<?php echo htmlspecialchars($promotion['p_s_date'] ?? ''); ?>" required></td>
        </tr>
        <tr>
            <th>End Date</th>
            <td colspan="2"><input type="date" name="p_e_date" value="<?php echo htmlspecialchars($promotion['p_e_date'] ?? ''); ?>" required></td>
        </tr>
        <tr>
            <th>Promotion Type</th>
            <td>
                <input type="text" id="rateInput" name="rateInput" value="<?php echo htmlspecialchars($promotion['p_rate_price'] ?? ''); ?>" placeholder="Enter Rate Price" style="display: none;">
                <input type="text" id="priceInput" name="priceInput" readonly placeholder="Enter Price" style="display: none;">
                <select name="promotionType" id="promotionType" onchange="togglePromotionInput()">
                    <option value="">Select</option>
                    <option value="Price" <?php echo (!$promotion['p_rate_price'] || is_numeric($promotion['p_rate_price'])) ? 'selected' : ''; ?>>Price</option>
                    <option value="Rate" <?php echo (is_numeric($promotion['p_rate_price'])) ? 'selected' : ''; ?>>Rate</option>
                </select>
            </td>
        </tr>
        <!-- Services Section -->
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
            echo "<tr>";
            echo "<td colspan='2'>";
            echo "<details open>";
            echo "<summary>$cat_name</summary>";

            if (isset($services[$cat])) {
                foreach ($services[$cat] as $index => $service) {
                    $service_name = htmlspecialchars($service['si_service_name'] ?? '');
                    $price = htmlspecialchars($service['si_price'] ?? '');
                    $service_value = $cat . "|" . $service_name . "|" . $price;
                    $serviceId = $cat . "-" . $index;

                    // Determine if the service was previously selected
                    $checked = isset($existing_services[$cat][$service['si_no']]) ? 'checked' : '';
                    echo "<input type='checkbox' id='$serviceId' name='selected_services[]' value='$service_value' $checked onchange=\"toggleServiceInput('$serviceId')\"> $service_name - " . number_format($price);
                    echo "<div id='input-$serviceId' style='display: none;'><input type='text' name='pro_s_price[$service_value]' placeholder='Enter Promotion price' value=\"" . (htmlspecialchars($_POST['pro_s_price'][$service_value] ?? '')) . "\"></div><br>";
                }
            } else {
                echo "<p>No services available for $cat_name.</p>";
            }

            echo "</details>";
            echo "</td>";
            echo "</tr>";
        }
        ?>
    </table>
    <div class="buttons">
            <button type="submit" class="page-btn">Save</button>
            <button type="button" class="page-btn" onclick="window.location.href='promotion_list.php'">Cancel</button>
        </div>
</form>
</div>
</body>
</html>
