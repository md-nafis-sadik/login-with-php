<?php
include 'db.php';

if (isset($_GET['id'])) {
    $userId = $_GET['id'];

    $sql = "SELECT image FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($imageBase64);

    if ($stmt->num_rows > 0) {
        $stmt->fetch();

        $imageData = base64_decode($imageBase64);


        header("Content-Type: image/jpeg");  
        echo $imageData;
    } else {

        header("HTTP/1.1 404 Not Found");
        echo "Image not found.";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
?>
