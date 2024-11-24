<?php
session_start();
require_once 'vendor/autoload.php';
include 'config.php';

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

if (isset($_COOKIE['auth_token'])) {
    try {
        $jwt = $_COOKIE['auth_token'];

        $decoded = JWT::decode($jwt, new Key($secret_key, 'HS256'));

        $email = $decoded->email;
        $userId = $decoded->id;

        echo "
            <!DOCTYPE html>
            <html lang='en'>
            <head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <title>Welcome</title>
                <!-- Bootstrap CSS -->
                <link href='https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css' rel='stylesheet'>
            </head>
            <body class='bg-light'>

            <div class='d-flex shadow-sm justify-content-between px-4 py-2 bg-primary'><h3 class='text-white'>Home</h3><a href='logout.php' class='btn btn-light font-semibold btn-md'>Logout</a></div>
                <div class='container my-5'>
                    <div class='card shadow-sm p-4'>
                        <h2 class='text-primary'>Welcome, $email!</h2>
                        <p class='lead'>You are logged in. Your User ID is: <strong>$userId</strong></p>

                    </div>
                </div>

                <!-- Optional Bootstrap JS (for interactivity) -->
                <script src='https://code.jquery.com/jquery-3.5.1.slim.min.js'></script>
                <script src='https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js'></script>
                <script src='https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js'></script>

            </body>
            </html>";
        exit();
    } catch (Exception $e) {

        echo "Invalid session. Please log in again.";
        setcookie("auth_token", "", time() - 3600, "/", "", true, true); // Clear the cookie
        header("Location: login.php");
        exit();
    }
} else {

    echo "You are not logged in. Redirecting to login page...";
    header("Location: login.php");
    exit();
}
