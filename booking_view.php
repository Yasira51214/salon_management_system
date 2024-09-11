<?php
include 'common.php';
include "side_bar.php";
// Get o_no from the GET request
$o_no = $_GET['o_no'] ?? '';

if (empty($o_no)) {
    die("Order number is required.");
}

// Fetch data from the order, orderservice, and service_item tables
$sql = "
    SELECT 
        o.o_memo, 
        o.o_amount,
        si.si_cat, 
        si.si_service_name AS service_name, 
        os.s_qty, 
        os.s_price 
    FROM 
        `order` o
    JOIN 
        orderservice os 
    ON 
        o.o_no = os.s_o_no
    JOIN 
        service_item si
    ON 
        os.s_si_no = si.si_no
    WHERE 
        o.o_no = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $o_no);
$stmt->execute();
$result = $stmt->get_result();

// Check if the query was successful
if (!$result) {
    die("Query failed: " . $conn->error);
}

// Define categories
$categories = [
    'N' => 'Nail',
    'P' => 'Pedicure',
    'E' => 'Eyelashes',
    'M' => 'Massage',
    'T' => 'Training',
    'S' => 'Sales'
];

$services = [];
$memo = "";
$totalAmount = 0.00;

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $cat = $row["si_cat"];
        if (array_key_exists($cat, $categories)) {
            $services[$cat][] = [
                'name' => $row["service_name"],
                'qty' => $row["s_qty"],
                'price' => $row["s_price"]
            ];
        }
        $memo = $row["o_memo"];
        $totalAmount = $row["o_amount"];
    }
} else {
    echo "No booking found for this order.";
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


$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking View</title>
    <link rel="stylesheet" href="./css/order_view.css">
    
    <style>
    /* Insert the CSS styles from the order_view.php */
    .container {
        padding: 60px;
        margin: 0 auto;
        max-width: 1200px;
    }
    h1 {
        text-align: center;
        font-size: 40px;
        color: #22584b;
    }
    .category {
        margin-bottom: 30px;
    }
    .category h2 {
        margin-bottom: 10px;
    }
    .service-table {
        width: 100%;
        border-collapse: collapse;
    }
    .service-table th, .service-table td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }
    .service-table th {
        background-color: #f25a9c;
        color: #fff;
    }
    .description {
        margin-bottom: 30px;
        padding: 10px;
        border: 1px solid #ddd;
        background-color: #f25a9c;
        color: white;
    }
    .total-amount {
        margin-top: 20px;
        padding: 10px;
        border: 1px solid #ddd;
        background-color: #f25a9c;
        font-weight: bold;
        text-align: right;
        color: white;
    }
    
    </style>
</head>
<body>

<div class="container">
    <h1 style="color: #cd015b;">Booking View</h1>
    <?php foreach ($categories as $catCode => $catName): ?>
        <div class="category">
            <h2><?php echo $catName; ?></h2>
            <?php if (isset($services[$catCode])): ?>
                <table class="service-table">
                    <tr>
                        <th>Service</th>
                        <th>Quantity</th>
                        <th>Price</th>
                    </tr>
                    <?php foreach ($services[$catCode] as $service): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($service['name']); ?></td>
                            <td><?php echo 'x' . htmlspecialchars($service['qty']); ?></td>
                            <td><?php echo htmlspecialchars($service['price']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php else: ?>
                <p>No booking found for this category.</p>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>

    <h2>Note</h2>
    <div class="description">
        <?php echo htmlspecialchars($memo); ?>
    </div>

     <div class="total-amount">
        <!-- Uncomment and add currency if required -->
        Total Amount: <?php echo htmlspecialchars(number_format($totalAmount)); ?> &nbsp;<?php echo htmlspecialchars($currency); ?>
            </div>
      
            <div class="buttons">
            <a href="./booking_history.php"><button type="button" class="cancel" onclick="resetForm()">Cancel </button></a>
            </div>
        </form>
      
    </div>

</div>

<script>
function handleAction(action) {
    if (action === 'cancel') {
        window.location.href = 'order_main.php';
    }
}
</script>
</body>
</html>
