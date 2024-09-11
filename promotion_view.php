<?php include 'common.php';

// Retrieve the promotion ID from the query string
$promotion_id = $_GET['p_no'] ?? '';

if ($promotion_id) {
    // Fetch promotion data
    $stmt = $conn->prepare("SELECT * FROM promotion WHERE p_no = ?");
    $stmt->bind_param("i", $promotion_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $promotion = $result->fetch_assoc();
    $stmt->close();

    if (!$promotion) {
        die('Error: Promotion not found.');
    }

    // Fetch services associated with this promotion
    $stmt = $conn->prepare("
        SELECT ps.*, si.si_service_name,si.si_price 
        FROM pro_service ps 
        JOIN service_item si ON ps.pro_si_no = si.si_no 
        WHERE ps.pro_p_no = ?
    ");
    $stmt->bind_param("i", $promotion_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $services = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // Fetch the previous promotion ID
    $stmt = $conn->prepare("SELECT p_no FROM promotion WHERE p_no < ? ORDER BY p_no DESC LIMIT 1");
    $stmt->bind_param("i", $promotion_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $prev_promotion = $result->fetch_assoc();
    $prev_promotion_id = $prev_promotion['p_no'] ?? null;
    $stmt->close();

    // Fetch the next promotion ID
    $stmt = $conn->prepare("SELECT p_no FROM promotion WHERE p_no > ? ORDER BY p_no ASC LIMIT 1");
    $stmt->bind_param("i", $promotion_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $next_promotion = $result->fetch_assoc();
    $next_promotion_id = $next_promotion['p_no'] ?? null;
    $stmt->close();
} else {
    die('Error: No promotion ID provided.');
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Promotion View</title>
    <link rel="stylesheet" href="css/promotion_add.css">
</head>
<body>
    <?php include "side_bar.php"; ?>
    <div class="container">
        <h1>Promotion View</h1>
        <table class="customer-table">
            <tr>
                <th>Promotion Name</th>
                <td colspan="2"><input type="text" value="<?php echo htmlspecialchars($promotion['p_name']); ?>" readonly></td>
            </tr>
            <tr>
                <th>Start Date</th>
                <td colspan="2"><input type="date" value="<?php echo htmlspecialchars($promotion['p_s_date']); ?>" readonly></td>
            </tr>
            <tr>
                <th>End Date</th>
                <td colspan="2"><input type="date" value="<?php echo htmlspecialchars($promotion['p_e_date']); ?>" readonly></td>
            </tr>
            <tr>
                <th>Promotion Type</th>
                <td>
                    <select name="promotion_type" disabled>
                        <option value="Rate" <?php echo ($promotion['p_rate_price'] !== '') ? 'selected' : ''; ?>>Rate</option>
                        <option value="Price" <?php echo ($promotion['p_rate_price'] === '') ? 'selected' : ''; ?>>Price</option>
                    </select>
                    <?php if ($promotion['p_rate_price'] !== ''): ?>
                        <input type="text" value="<?php echo htmlspecialchars($promotion['p_rate_price']); ?>" readonly>
                    <?php endif; ?>
                </td>
            </tr>
            <!-- Display associated services -->
            <?php foreach ($services as $service): ?>
            <tr>
                    <td colspan="2">
                <details open>
                    <summary><?php echo htmlspecialchars($service['pro_s_cat']); ?>
                    <?php echo $service['si_price']; ?>/ <?php echo $service['pro_s_price'];?>
                
                </summary>
                 
                    <input type="checkbox" checked disabled><?php echo htmlspecialchars($service['si_service_name']); ?><br>
                </details>
            </td>
            </tr>
            <?php endforeach; ?>
        </table>

        <!-- Navigation Buttons -->
        <div class="navigation-buttons">
            <?php if ($prev_promotion_id): ?>
                <a href="promotion_view.php?p_no=<?php echo $prev_promotion_id; ?>"><button class="page-btn">Previous</button></a>
            <?php endif; ?>
            <?php if ($next_promotion_id): ?>
                <a href="promotion_view.php?p_no=<?php echo $next_promotion_id; ?>"><button class="page-btn">Next</button></a>
            <?php endif; ?>
     

        <!-- Delete and Cancel Buttons -->
        <form method='POST' action='delete_promotion.php' onsubmit='return confirm("Are you sure you want to delete this promotion?");' style='display:inline;'>
            <input type='hidden' name='p_no' value='<?php echo $promotion_id; ?>'>
            <button type='submit' class='page-btn'>Delete</button>
        </form>
        <a href="promotion_list.php"><button class="page-btn">Cancel</button></a>
    </div>
</body>
</html>
