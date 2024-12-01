<?php
include 'db.php';

if (empty($_POST['users'])) {
    $message = "Please fill in all the fields.";
} else {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $users = $_POST['users'];  // Assuming 'users' is an array passed from the form

// var_dump($users);
         
//         exit();
        foreach ($users as $index => $user) {
            // Get each user's data
            $fullName = $user['full_name'];
            $username = $user['username'];
            $email = $user['email'];
            $phoneNumber = $user['phone_number'];
            $division_id = $user['division_id'];
            $district_id = $user['district_id'];
            $thana_id = $user['thana_id'];

            if (!preg_match("/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/", $user['password'])) {
                $message = "Password must be at least 8 characters, with letters and numbers. ";
                break;
            }


            $password = password_hash($user['password'], PASSWORD_BCRYPT);
            $address = $user['address'];



            // // Validation checks
            if (!preg_match("/^[a-zA-Z\s]+$/", $fullName)) {
                $message = "Full Name can only contain letters and spaces.";
                break;  // Stop the loop and show the message
            }

            if (!preg_match("/^[a-zA-Z0-9]{4,20}$/", $username)) {
                $message = "Username must be 4-20 alphanumeric characters.";
                break;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $message = "Invalid email format.";
                break;
            }

            if (!preg_match("/^\d{10,15}$/", $phoneNumber)) {
                $message = "Phone number must be 10-15 digits.";
                break;
            }


            
            

            // // Handle image for each user
            $imageBase64 = null;
            if (isset($_FILES['images']['tmp_name'][$index]) && $_FILES['images']['error'][$index] === 0) {
                $imageData = file_get_contents($_FILES['images']['tmp_name'][$index]);
                $imageBase64 = base64_encode($imageData);
            }

            // Prepare the SQL insert statement
            $sql = "INSERT INTO users (full_name, username, email, phone_number, password, address, image ,division_id, district_id, thana_id) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssssssss", $fullName, $username, $email, $phoneNumber, $password, $address, $imageBase64, $division_id, $district_id, $thana_id);

            if (!$stmt->execute()) {
                $message = "Error adding user.";
                break;  // If any user fails to be added, break the loop
            }
        }

        // Provide feedback after the loop
        if (isset($message)) {
            header("Location: welcome.php?message=" . urlencode($message));
        } else {
            header("Location: welcome.php?message=Users added successfully");
        }

        $stmt->close();
    }
    $conn->close();
}
?>
