<?php
include 'common.php';

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
    while($row = $result->fetch_assoc()) {
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
    echo "No services found for this order.";
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
    <title>Order View</title>
    <link rel="stylesheet" href="./css/order_view.css">
    <style>
        .container {
            padding: 20px;
            margin: 0 auto;
            max-width: 1200px;
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
            background-color: #ef5a9b;
            color: #fff;
        }
        .description {
            margin-bottom: 30px;
            padding: 10px;
            border: 1px solid #ddd;
            background-color: #ef5a9b;
            color: white;
        }
        .total-amount {
            margin-top: 20px;
            padding: 10px;
            color: white;
            border: 1px solid #ddd;
            background-color: #ef5a9b;
            font-weight: bold;
            text-align: right;
          
        }
        .buttons {
            display: flex;
            gap: 10px;
        }
        .buttons button, .buttons a {
            padding: 10px 20px;
            text-decoration: none;
            color: #fff;
            background-color: #cd015b;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }
        .buttons button:hover, .buttons a:hover {
            background-color: #ef5a9b;
        }
        .buttons form {
            display: inline;
          
        }
    </style>
</head>
<body>
<?php include "side_bar.php" ?>

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
                            <td><?php echo 'x'; ?><?php echo htmlspecialchars($service['qty']); ?></td>
                            <td><?php echo htmlspecialchars($service['price']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php else: ?>
                <p>No services found for this category.</p>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>

    <h2>Note</h2>
    <div class="description">
        <?php echo htmlspecialchars($memo); ?>
    </div>

    <div class="total-amount">
        Total Amount: <?php echo htmlspecialchars(number_format($totalAmount)); ?> &nbsp;<?= htmlspecialchars($currency) ?>
    </div>

    <div class="buttons">
        <a href="order_modify.php?o_no=<?php echo htmlspecialchars($o_no); ?>&totalAmount=<?php echo urlencode($totalAmount); ?>">Modify</a>
        <form action="delete_order.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this order?');">
            <input type="hidden" name="o_no" value="<?php echo htmlspecialchars($o_no); ?>">
            <button type="submit">Delete</button>
        </form>
        <button onclick="handleAction('cancel')">Cancel</button>
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
