<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .image-preview {
            max-width: 100px;
            max-height: 100px;
            border: 1px solid #ddd;
            margin-top: 20px;
        }
    </style>
</head>
<body class="bg-light">

    <div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
        <div class="card shadow-sm p-4" style="width: 100%; max-width: 500px;">
            <h2 class="text-center mb-4">Register</h2>
            <?php
            if (isset($_GET['message'])){
                $message = htmlspecialchars($_GET['message']);
                $alertClass = strpos($message, 'successful') !== false ? 'success' : 'danger';
                echo '<div class="alert alert-' . $alertClass . ' mt-3">' . $message . '</div>';
            }
            ?>
            <form method="POST" id="registrationForm" action="registration_process.php" enctype="multipart/form-data">
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

                <!-- Image Upload Field -->
                <div class="form-group">
                    <label for="image">Profile Image:</label>
                    <div class="d-flex">
                        <input type="file" class="form-control" id="image" name="image" onchange="previewImage(event)">
                    </div>
                    <img id="imagePreview" class="image-preview" src="#" alt="Image Preview" style="display: none;">
                </div>

                <!-- Address Field -->
                <div class="form-group">
                    <label for="address">Address:</label>
                    <textarea class="form-control" id="address" name="address" required></textarea>
                </div>

                <button type="submit" class="btn btn-block"  style="background-color: #024224; color:white;">Register</button>
                <div class="mt-3">Already have an account? <a href='login.php' class='' style="color: green;">Login</a></div>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="script.js"></script>

    <!-- Image Preview Script -->
    <script>
    function previewImage(event) {
        var file = event.target.files[0];
        var reader = new FileReader();

        reader.onload = function() {
            var imagePreview = document.getElementById("imagePreview");
            imagePreview.src = reader.result;
            imagePreview.style.display = "block";
        }

        if (file) {
            reader.readAsDataURL(file);
        }
    }


    document.getElementById('registrationForm').addEventListener('submit', function (event) {
    let isValid = true;
    const name = document.getElementById('full_name').value.trim();
    const username = document.getElementById('username').value.trim();
    const email = document.getElementById('email').value.trim();
    const phone = document.getElementById('phone_number').value.trim();
    const password = document.getElementById('password').value;
    

    // Full Name: Only letters and spaces
    if (!/^[A-Za-z\s]+$/.test(name)) {
        isValid = false;
        alert("Full Name can only contain letters and spaces.");
    }

    // Username: Alphanumeric and 4-20 characters
    if (!/^[a-zA-Z0-9]{4,20}$/.test(username)) {
        isValid = false;
        alert("Username must be 4-20 alphanumeric characters.");
    }

    // Email: Valid format
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        isValid = false;
        alert("Enter a valid email address.");
    }

    // Phone Number: Digits only (10-15 digits)
    if (!/^\d{10,15}$/.test(phone)) {
        isValid = false;
        alert("Phone number must be 10-15 digits.");
    }

    // Password: Minimum 8 characters, including letters and numbers
    if (!/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/.test(password)) {
        isValid = false;
        alert("Password must be at least 8 characters, with letters and numbers.");
    }

    if (!isValid) {
        event.preventDefault(); // Stop form submission if validation fails
    }
});

    </script>
</body>
</html>
