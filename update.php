<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   
    $userId = $_POST['user_id'];
    $fullName = trim($_POST['full_name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $phoneNumber = trim($_POST['phone_number']);
    $address = trim($_POST['address']);
    $password = $_POST['password'] ?? null;


    if (empty($fullName) || empty($email) || empty($username) || empty($phoneNumber) || empty($address)) {
        die("All fields are required.");
    }
    if (!preg_match("/^[a-zA-Z\s]+$/", $fullName)) {
        die("Full Name can only contain letters and spaces.");
    }
    if (!preg_match("/^[a-zA-Z0-9]{4,20}$/", $username)) {
        die("Username must be 4-20 alphanumeric characters.");
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format.");
    }
    if (!preg_match("/^\d{10,15}$/", $phoneNumber)) {
        die("Phone number must be 10-15 digits.");
    }


    $passwordHash = null;
    if (!empty($password)) {
        if (!preg_match("/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/", $password)) {
            die("Password must be at least 8 characters, including letters and numbers.");
        }
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
    }


    $imageBase64 = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $imageData = file_get_contents($_FILES['image']['tmp_name']);
        $imageBase64 = base64_encode($imageData);  
    }

    $updateFields = [
        "full_name = ?",
        "username = ?",
        "email = ?",
        "phone_number = ?",
        "address = ?",
    ];
    $params = [$fullName, $username, $email, $phoneNumber, $address];
    $types = "sssss";


    if ($passwordHash) {
        $updateFields[] = "password = ?";
        $params[] = $passwordHash;
        $types .= "s";
    }


    if ($imageBase64) {
        $updateFields[] = "image = ?";
        $params[] = $imageBase64;
        $types .= "s";
    }


    $updateFieldsString = implode(", ", $updateFields);
    $sql = "UPDATE users SET $updateFieldsString WHERE id = ?";
    $params[] = $userId;
    $types .= "i";


    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        header("Location: welcome.php?message=User updated successfully");
    } else {
        header("Location: welcome.php?message=Error updating user");
    }

    $stmt->close();
    $conn->close();
} else {
    die("Invalid request method.");
}
?>
