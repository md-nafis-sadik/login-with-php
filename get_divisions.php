<?php
// get_divisions.php
include('db_connection.php');
$query = "SELECT id, name FROM divisions";
$result = mysqli_query($conn, $query);

$divisions = [];
while ($row = mysqli_fetch_assoc($result)) {
    $divisions[] = $row;
}

echo json_encode($divisions);
?>
