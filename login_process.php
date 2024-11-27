<?php

session_start();
include('db.php');
require_once 'vendor/autoload.php';

use \Firebase\JWT\JWT;

include('config.php');

$error = ""; // Initialize error variable

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = htmlspecialchars($_POST['email']);
    $password = htmlspecialchars($_POST['password']);

    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        $error = "Database error: " . $conn->error;
    } else {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();

            if (password_verify($password, $row['password'])) {

                $_SESSION['user_id'] = $row['id'];
                $_SESSION['email'] = $row['email'];


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
                $error = "Incorrect password!";
            }
        } else {
            $error = "No user found with that email!";
        }

        $stmt->close();
    }

    header("Location: login.php?message=" . urlencode($error));
    exit;
}

$conn->close();
?>