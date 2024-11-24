<?php

session_start();
include('db.php');
require_once 'vendor/autoload.php';

use \Firebase\JWT\JWT;

include('config.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {

        $row = $result->fetch_assoc();

        if (password_verify($password, $row['password'])) {
            $issued_at = time();
            $expiration_time = $issued_at + 3600;
            $payload = array(
                "iat" => $issued_at,
                "exp" => $expiration_time,
                "email" => $row['email'],
                "id" => $row['id']
            );

            $jwt = JWT::encode($payload, $secret_key, 'HS256');

            setcookie("auth_token", $jwt, $expiration_time, "/", "", true, true);

            header("Location: welcome.php");
            exit();
        } else {
            echo "No user found with that email!";
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
    <title>Login</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
        <div class="card shadow-sm p-4" style="width: 100%; max-width: 400px;">
            <h2 class="text-center mb-4">Login</h2>
            <form method="POST">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Login</button>

                <div class="mt-3">Dont have an account? <a href='register.php' class=''>Register</a></div>
            </form>

            <?php
            if (isset($error)) {
                echo '<div class="alert alert-danger mt-3">' . $error . '</div>';
            }
            ?>

        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
