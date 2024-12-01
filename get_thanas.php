<?php
include 'db.php'; // Include database connection

if (isset($_GET['district_id'])) {
    $district_id = intval($_GET['district_id']); // Sanitize input

    // Fetch thanas associated with the district
    $sql = "SELECT id, name FROM thanase WHERE district_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $district_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $thanas = [];
    while ($row = $result->fetch_assoc()) {
        $thanas[] = $row;
    }

    echo json_encode($thanas);
} else {
    echo json_encode([]); // Return an empty array if no district_id
}
?>
