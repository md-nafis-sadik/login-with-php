<?php
// Include the database connection
include 'db.php';

if (isset($_GET['id'])) {
    $userId = $_GET['id'];

    // SQL query to delete the user
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);

    // Execute the statement and check for success
    if ($stmt->execute()) {
        // Redirect to the users page after deletion with success message
        header("Location: welcome.php?message=User%20deleted%20successfully");
        exit();
    } else {
        // Error handling and redirection with failure message
        header("Location: welcome.php?message=Error%20deleting%20user");
        exit();
    }

    // Close the prepared statement
    $stmt->close();
} else {
    // Redirect if no user ID is passed
    echo "No user ID specified.";
}

$conn->close();
?>
