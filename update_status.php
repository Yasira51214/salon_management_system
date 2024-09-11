// update_status.php
<?php
include 'common.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $p_no = $_POST['p_no'];
    $new_status = $_POST['status'];

    // Begin a transaction
    $conn->begin_transaction();

    try {
        // Deactivate all promotions if the new status is activate
        if ($new_status == 1) {
            $deactivate_stmt = $conn->prepare("UPDATE promotion SET status = 0 WHERE p_no != ?");
            $deactivate_stmt->bind_param("i", $p_no);
            $deactivate_stmt->execute();
            $deactivate_stmt->close();
        }

        // Update the status of the selected promotion
        $stmt = $conn->prepare("UPDATE promotion SET status = ? WHERE p_no = ?");
        $stmt->bind_param("ii", $new_status, $p_no);
        $stmt->execute();
        $stmt->close();

        if ($new_status == 1) {
            // Fetch the promotion details
            $stmt = $conn->prepare("
                SELECT ps.pro_si_no, ps.pro_s_price 
                FROM pro_service ps 
                WHERE ps.pro_p_no = ?
            ");
            $stmt->bind_param("i", $p_no);
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                $si_no = $row['pro_si_no'];
                $promotion_price = $row['pro_s_price'];

                // Update the si_promotion_price in service_item table
                $update_stmt = $conn->prepare("UPDATE service_item SET si_promotion_price = ? WHERE si_no = ?");
                $update_stmt->bind_param("di", $promotion_price, $si_no);
                $update_stmt->execute();
                $update_stmt->close();
            }

            $stmt->close();
        } else {
            // If promotion is deactivated, reset the si_promotion_price to NULL
            $stmt = $conn->prepare("
                SELECT ps.pro_si_no 
                FROM pro_service ps 
                WHERE ps.pro_p_no = ?
            ");
            $stmt->bind_param("i", $p_no);
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                $si_no = $row['pro_si_no'];

                // Reset the si_promotion_price to NULL in service_item table
                $update_stmt = $conn->prepare("UPDATE service_item SET si_promotion_price = NULL WHERE si_no = ?");
                $update_stmt->bind_param("i", $si_no);
                $update_stmt->execute();
                $update_stmt->close();
            }

            $stmt->close();
        }

        // Commit the transaction
        $conn->commit();

        // Redirect back to the promotion list
        header("Location: promotion_list.php");
        exit();
    } catch (Exception $e) {
        // Rollback the transaction in case of an error
        $conn->rollback();
        echo "Error updating promotion status: " . $e->getMessage();
    }
}
?>
