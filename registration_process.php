<?php
include('db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $message = "";

    // Check if the form fields are empty
    if (empty($_POST['full_name']) || empty($_POST['email']) || empty($_POST['username']) || empty($_POST['phone_number']) || empty($_POST['password'])) {
        $message = "Please fill in all the fields.";
    } else {
        // Sanitize input
        $full_name = htmlspecialchars($_POST['full_name']);
        $email = htmlspecialchars($_POST['email']);
        $username = htmlspecialchars($_POST['username']);
        $phone_number = htmlspecialchars($_POST['phone_number']);
        $address = htmlspecialchars($_POST['address']);
        $password = htmlspecialchars($_POST['password']);
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Backend validation
        if (!preg_match("/^[a-zA-Z\s]+$/", $full_name)) {
            $message = "Full Name can only contain letters and spaces.";
        }

        if (!preg_match("/^[a-zA-Z0-9]{4,20}$/", $username)) {
            $message = "Username must be 4-20 alphanumeric characters.";
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = "Invalid email format.";
        }

        if (!preg_match("/^\d{10,15}$/", $phone_number)) {
            $message = "Phone number must be 10-15 digits.";
        }

        if (!preg_match("/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/", $password)) {
            $message = "Password must be at least 8 characters, with letters and numbers.";
        }

        if (empty($message)) {  
            $check_email_sql = "SELECT * FROM users WHERE email = ?";
            $stmt = $conn->prepare($check_email_sql);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $message = "This email is already registered. Please use a different email.";
            } else {

                $image_location = null;
                if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                    $image_dir = 'uploads/'; // The folder where you want to store the image
                    $image_name = basename($_FILES['image']['name']);
                    $image_path = $image_dir . $image_name;

                    // Move the uploaded file to the target directory
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $image_path)) {
                        $image_location = $image_path; // Store the image location
                    } else {
                        $message = "Failed to upload image.";
                    }
                }

                // Insert into the database
                $insert_sql = "INSERT INTO users (email, password, username, full_name, phone_number, image_location, address) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($insert_sql);
                $stmt->bind_param("sssssss", $email, $hashed_password, $username, $full_name, $phone_number, $image_location, $address);

                if ($stmt->execute()) {
                    $message = "Registration successful!";
                } else {
                    $message = "Error: " . $stmt->error;
                }
            }

            $stmt->close();
        }
    }

    // Redirect back to the form with a message
    header("Location: register.php?message=" . urlencode($message));
    exit;
}

$conn->close();
?>
