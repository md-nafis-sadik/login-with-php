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
                // Insert into the database
                $insert_sql = "INSERT INTO users (email, password, username, full_name, phone_number) VALUES (?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($insert_sql);
                $stmt->bind_param("sssss", $email, $hashed_password, $username, $full_name, $phone_number);

                if ($stmt->execute()) {
                    $message = "Registration successful!";
                } else {
                    $message = "Error: " . $stmt->error;
                }
            }

            $stmt->close();
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body class="bg-light">

    <div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
        <div class="card shadow-sm p-4" style="width: 100%; max-width: 500px;">
            <h2 class="text-center mb-4">Register</h2>
            <?php
            if (!empty($message)) {
                echo '<div class="alert alert-' . (strpos($message, 'successful') !== false ? 'success' : 'danger') . ' mt-3">' . $message . '</div>';
            }
            ?>
            <form method="POST" id="registrationForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                <div class="form-group">
                    <label for="full_name">Full Name:</label>
                    <input type="text" class="form-control" id="full_name" name="full_name" required>
                </div>

                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="phone_number">Phone Number:</label>
                    <input type="tel" class="form-control" id="phone_number" name="phone_number" required pattern="^\d{10,15}$">
                </div>

                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Register</button>
                <div class="mt-3">Already have an account? <a href='login.php' class=''>Login</a></div>
            </form>

            

        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="script.js"></script>
</body>
</html>
