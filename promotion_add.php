        <?php
        include 'common.php';

        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        // Fetch service items from the database including si_no
        $sql = "SELECT si_no, si_cat, si_service_name, si_price FROM service_item";
        $result = $conn->query($sql);

        $services = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $services[$row['si_cat']][] = $row;
            }
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

            // Validate required fields
            if (empty($p_name) || empty($p_s_date) || empty($p_e_date) || empty($promotionType)) {
                die('Error: Please fill in all required fields.');
            }

            if ($promotionType === "Rate" && empty($p_rate_price)) {
                die('Error: Please fill in the rate price for Rate promotion type.');
            }

            if ($promotionType === "Price" && empty($selected_services)) {
                die('Error: Please select at least one service for Price promotion type.');
            }

            // Insert promotion data into the promotion table
            $stmt = $conn->prepare("INSERT INTO promotion (p_name, p_s_date, p_e_date, p_s_items, p_rate_price) VALUES (?, ?, ?, ?, ?)");
            if (!$stmt) {
                die('Prepare failed: ' . $conn->error);
            }

            $p_s_items = 0;
            $stmt->bind_param("sssis", $p_name, $p_s_date, $p_e_date, $p_s_items, $p_rate_price);

            if (!$stmt->execute()) {
                die('Execute failed: ' . $stmt->error);
            }

            $promotion_id = $stmt->insert_id;
            $stmt->close();

            // Insert selected services into the pro_service table
            $stmt = $conn->prepare("INSERT INTO pro_service (pro_p_no, pro_s_cat, pro_si_no, pro_s_price) VALUES (?, ?, ?, ?)");
            if (!$stmt) {
                die('Prepare failed: ' . $conn->error);
            }

            $service_count = 0;
            foreach ($selected_services as $service) {
                list($cat, $name, $price) = explode('|', $service);
                $input_service_id = $cat . '-' . array_search($service, $selected_services);

                // Fetch the si_no from the services array based on cat and name
                $si_no = null;
                foreach ($services[$cat] as $item) {
                    if ($item['si_service_name'] === $name) {
                        $si_no = $item['si_no'];
                        break;
                    }
                }

                // If si_no is not found, skip this service
                if (!$si_no) {
                    continue;
                }

                $promotionType = $_POST['promotionType'] ?? '';
                $rateInput = $_POST['rateInput'] ?? 0; // Default to 0 if not provided
                
                // Calculate the promotion price for the 'Rate' promotion type
                if ($promotionType === 'Rate') {
                    // Use the rate input value instead of random rate
                    $promotion_rate = (float)$rateInput;
                    $promotion_price = $price - ($price * ($promotion_rate / 100));
                } else {
                    // For 'Price' type, use the provided promotion price or default to the original price
                    $promotion_price = $pro_s_prices[$input_service_id] ?? $price;
                }
                // Store the si_no in the pro_si_no field
                $stmt->bind_param("isii", $promotion_id, $cat, $si_no, $promotion_price);

                if (!$stmt->execute()) {
                    die('Execute failed: ' . $stmt->error);
                }

                $service_count++;
            }

            $stmt->close();

            // Update the promotion table with the actual number of services
            $stmt = $conn->prepare("UPDATE promotion SET p_s_items = ? WHERE p_no = ?");
            if (!$stmt) {
                die('Prepare failed: ' . $conn->error);
            }

            $stmt->bind_param("ii", $service_count, $promotion_id);

            if (!$stmt->execute()) {
                die('Execute failed: ' . $stmt->error);
            }

            $stmt->close();
            $conn->close();

            header("Location: promotion_list.php");
            exit();
        }
        ?>


                <!DOCTYPE html>
                <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Promotion Add</title>
                    <link rel="stylesheet" type="text/css" href="./css/promotion_add.css">
                    <script>
                        function togglePromotionInput() {
                            var promotionType = document.getElementById("promotionType").value;
                            var rateInput = document.getElementById("rateInput");
                            var priceInputs = document.querySelectorAll(".service-price-input");

                            rateInput.style.display = "none";

                            priceInputs.forEach(function(input) {
                                input.style.display = "none";
                            });

                            if (promotionType === "Rate") {
                                rateInput.style.display = "inline-block";
                            } else if (promotionType === "Price") {
                                priceInputs.forEach(function(input) {
                                    input.style.display = "none"; // Hide initially
                                });
                            }
                        }

                        function togglePromotionInput() {
                            var promotionType = document.getElementById("promotionType").value;
                            var rateInput = document.getElementById("rateInput");
                            var priceInputs = document.querySelectorAll(".service-price-input");
                            var checkboxes = document.querySelectorAll(".service-checkbox");

                            // Hide all inputs initially
                            rateInput.style.display = "none";
                            priceInputs.forEach(function(input) {
                                input.style.display = "none";
                            });

                            if (promotionType === "Rate") {
                                // Show rate input and uncheck all checkboxes
                                rateInput.style.display = "inline-block";
                                checkboxes.forEach(function(checkbox) {
                                    if (checkbox.checked) {
                                        checkbox.checked = false; // Uncheck the checkbox
                                        toggleServiceInput(checkbox.id); // Hide the associated input field
                                    }
                                });
                            } else if (promotionType === "Price") {
                                // Hide rate input and uncheck all checkboxes
                                rateInput.style.display = "none";
                                checkboxes.forEach(function(checkbox) {
                                    if (checkbox.checked) {
                                        checkbox.checked = false; // Uncheck the checkbox
                                        toggleServiceInput(checkbox.id); // Hide the associated input field
                                    }
                                });
                            }
                        }

                        function toggleServiceInput(serviceId) {
                            const checkbox = document.getElementById(serviceId);
                            const inputField = document.getElementById('input-' + serviceId);
                            const promotionType = document.getElementById('promotionType').value;

                            if (promotionType === 'Price') {
                                inputField.style.display = checkbox.checked ? 'inline-block' : 'none';
                            } else {
                                inputField.style.display = 'none';
                            }
                        }

                        function updateServiceCount(cat) {
                            const checkboxes = document.querySelectorAll(`input[data-cat="${cat}"]:checked`);
                            const countDisplay = document.getElementById(`${cat}-count`);
                            countDisplay.textContent = checkboxes.length;
                        }

                        document.addEventListener('DOMContentLoaded', function() {
                            togglePromotionInput();

                            document.querySelectorAll('.service-checkbox').forEach(function(checkbox) {
                                checkbox.addEventListener('change', function() {
                                    const cat = this.dataset.cat;
                                    toggleServiceInput(this.id);
                                    updateServiceCount(cat);
                                });
                            });
                        });


                    </script>
                </head>
                <body>
                <?php include "side_bar.php"; ?><br>

                <div class="container">
                    <h1>Promotion Add</h1>
                    <form method="POST" action="">
                        <table class="customer-table">
                            <tr>
                                <th>Promotion Name</th>
                                <td colspan="2"><input type="text" name="p_name" required></td>
                            </tr>
                            <tr>
                                <th>Start Date</th>
                                <td colspan="2"><input type="date" name="p_s_date" required></td>
                            </tr>
                            <tr>
                                <th>End Date</th>
                                <td colspan="2"><input type="date" name="p_e_date" required></td>
                            </tr>
                            <tr>
                                <th>Promotion type</th>
                                <td>
                                    <div id="rateInputContainer" style="display: none;">
                                        <input type="text" id="rateInput" name="rateInput" placeholder="Enter Rate Price">
                                        <span id="percentageSign" style="display: none;">%</span>
                                    </div>
                                    <select name="promotionType" id="promotionType" onchange="togglePromotionInput()" required>
                                        <option value="Select">Select</option>
                                        <option value="Price">Price</option>
                                        <option value="Rate">Rate</option>
                                    </select>

                                    <script>
                                        function togglePromotionInput() {
                                            var promotionType = document.getElementById("promotionType").value;
                                            var rateInputContainer = document.getElementById("rateInputContainer");
                                            var rateInput = document.getElementById("rateInput");
                                            var percentageSign = document.getElementById("percentageSign");
                                            var priceInputs = document.querySelectorAll(".service-price-input");

                                            rateInputContainer.style.display = "none";
                                            rateInput.value = ""; // Reset the value
                                            percentageSign.style.display = "none";

                                            priceInputs.forEach(function(input) {
                                                input.style.display = "none";
                                            });

                                            if (promotionType === "Rate") {
                                                rateInputContainer.style.display = "inline-block";
                                                percentageSign.style.display = "inline";
                                            } else if (promotionType === "Price") {
                                                priceInputs.forEach(function(input) {
                                                    input.style.display = "none"; // Hide initially
                                                });
                                            }
                                        }

                                    </script>
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
                                echo "<details>";
                                echo "<summary>$cat_name <span id='{$cat}-count'>(0)</span></summary>";

                                if (isset($services[$cat])) {
                                    foreach ($services[$cat] as $index => $service) {
                                        $service_name = htmlspecialchars($service['si_service_name'] ?? '');
                                        $price = htmlspecialchars($service['si_price'] ?? '');
                                        $service_value = $cat . "|" . $service_name . "|" . $price;
                                        $serviceId = $cat . "-" . $index;
                                        echo "<input type='checkbox' class='service-checkbox' data-cat='$cat' id='$serviceId' name='selected_services[]' value='$service_value' onchange=\"toggleServiceInput('$serviceId')\"> $service_name - " . number_format($price) . " RS";
                                        echo "<div id='input-$serviceId' class='service-price-input' style='display: none; left-margin: 15px;'><input type='text' name='pro_s_price[$serviceId]' placeholder='Enter Promotion price'></div><br>";
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