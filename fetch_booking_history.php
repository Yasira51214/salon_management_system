<?php
include 'common.php';

// Get the customer ID from the request
$customerId = isset($_GET['customer_id']) ? intval($_GET['customer_id']) : null;

if ($customerId) {
    // Function to fetch order history for a specific customer
    function getCustomerBookingHistory($customerId, $conn) {
        $sql = "
            SELECT o.o_date, si.si_service_name, o.o_amount
            FROM `order` o
            JOIN orderservice os ON o.o_no = os.s_o_no
            JOIN service_item si ON os.s_si_no = si.si_no
            WHERE o.o_c_no = ?
            ORDER BY o.o_date DESC
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $customerId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $bookings = [];
        while ($row = $result->fetch_assoc()) {
            $date = $row['o_date'];
            $service = $row['si_service_name'];

            // Initialize or append the service to the correct date
            if (!isset($bookings[$date])) {
                $bookings[$date] = [
                    'services' => [],
                    'amount' => $row['o_amount']
                ];
            }
            $bookings[$date]['services'][] = $service;
        }

        $stmt->close();
        return $bookings;
    }

    // Fetch booking history
    $bookings = getCustomerBookingHistory($customerId, $conn);

    // Output the booking history in HTML table rows
    if (!empty($bookings)) {
        $index = count($bookings); // Start index from the number of items
        foreach ($bookings as $date => $data) {
            echo "<tr>";
            echo "<td>" . ($index--) . "</td>"; // Decrement index for each row
            echo "<td>" . htmlspecialchars($date) . "</td>";
            echo "<td>" . htmlspecialchars(implode(', ', $data['services'])). "</td>";
            echo "<td>" . htmlspecialchars($data['amount']) . "</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='4'>No booking history found.</td></tr>";
    }
} else {
    echo "<tr><td colspan='4'>Invalid customer ID.</td></tr>";
}
?>
