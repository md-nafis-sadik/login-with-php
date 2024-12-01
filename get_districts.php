<?php
include 'db.php'; // Include database connection

if (isset($_GET['division_id'])) {
    $division_id = intval($_GET['division_id']); // Sanitize input

    // Fetch districts associated with the division
    $sql = "SELECT id, name FROM districts WHERE division_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $division_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $districts = [];
    while ($row = $result->fetch_assoc()) {
        $districts[] = $row;
    }

    echo json_encode($districts);
} else {
    echo json_encode([]); // Return an empty array if no division_id
}
?>
