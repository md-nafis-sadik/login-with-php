<?php
include 'db.php';

if (empty($_POST['full_name']) || empty($_POST['email']) || empty($_POST['username']) || empty($_POST['phone_number']) || empty($_POST['password'])) {
    $message = "Please fill in all the fields.";
} else {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $fullName = $_POST['full_name'];
        $username = $_POST['username'];
        $email = $_POST['email'];
        $phoneNumber = $_POST['phone_number'];
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $address = $_POST['address'];


        if (!preg_match("/^[a-zA-Z\s]+$/", $fullName)) {
            $message = "Full Name can only contain letters and spaces.";
        }

        if (!preg_match("/^[a-zA-Z0-9]{4,20}$/", $username)) {
            $message = "Username must be 4-20 alphanumeric characters.";
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = "Invalid email format.";
        }

        if (!preg_match("/^\d{10,15}$/", $phoneNumber)) {
            $message = "Phone number must be 10-15 digits.";
        }

        if (!preg_match("/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/", $password)) {
            $message = "Password must be at least 8 characters, with letters and numbers.";
        }


        $imageBase64 = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
 
            $imageData = file_get_contents($_FILES['image']['tmp_name']);
            $imageBase64 = base64_encode($imageData);  
        }

        // Prepare the SQL insert statement
        $sql = "INSERT INTO users (full_name, username, email, phone_number, password, address, image) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssss", $fullName, $username, $email, $phoneNumber, $password, $address, $imageBase64);

        if ($stmt->execute()) {
            header("Location: welcome.php?message=User added successfully");
        } else {
            header("Location: welcome.php?message=Error adding user");
        }

        $stmt->close();
    }
    $conn->close();
}
?>
