<?php
session_start();
require_once 'vendor/autoload.php';
include 'config.php';
include 'db.php'; // Include the database connection

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

// Session and JWT token validation
if (isset($_COOKIE['auth_token'])) {
    try {
        $jwt = $_COOKIE['auth_token'];
        $decoded = JWT::decode($jwt, new Key($secret_key, 'HS256'));

        if (isset($_SESSION['user_id']) && $_SESSION['email'] === $decoded->email) {
            $email = $_SESSION['email'];
            $userId = $_SESSION['user_id'];
        } else {
            setcookie("auth_token", "", time() - 3600, "/", "", true, true); // Clear the expired cookie
            header("Location: login.php");
            exit();
        }
    } catch (Exception $e) {
        setcookie("auth_token", "", time() - 3600, "/", "", true, true); // Clear the cookie
        header("Location: login.php");
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}

// Fetch users from the database
$sql = "SELECT * FROM users";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Add this in your head tag to use Font Awesome icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        .sidebar {
            height: 100vh;
            width: 250px;
            background-color: #022213;
            color: white;
            position: fixed;
            top: 0;
            left: 0;
            transform: translateX(-100%);
            transition: transform 0.3s ease;
            z-index: 1040;
            /* Above content but below modal */
        }

        .sidebar.active {
            transform: translateX(0);
        }

        .sidebar .nav-link {
            color: white;
        }

        .sidebar .nav-link:hover {
            background-color: #495057;
        }

        #overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            z-index: 1030;
            /* Below the sidebar */
        }

        #overlay.active {
            display: block;
        }

        @media (min-width: 768px) {
            .sidebar {
                transform: translateX(0);
                position: fixed;
                width: 250px;
            }

            #overlay {
                display: none !important;
            }

            .content {
                margin-left: 250px;
            }
        }

        .content {
            transition: margin-left 0.3s ease;
        }

        #view-users:hover{
            background-color: #024224;
            color: white;
            transition-delay: 5s;
            transition: background-color 0.5s ease, transform 0.5s ease;
        }
    </style>
</head>

<body class="bg-light">


    <!-- Sidebar -->
    <div id="sidebar" class="sidebar d-flex flex-column justify-content-between">
        <h4 class="pl-3 py-3 border-bottom">Dashboard</h4>
        <ul class="nav " >
            <li class="nav-item"  style="width: 100%; " >
                <a href="#" id="view-users" class="nav-link mx-2 " >Users</a>
            </li>
        </ul>
        <div class="mt-auto p-3">
            <a href="logout.php" class="btn " style="background-color: white; color: black; width:100%;">Logout</a>
        </div>
    </div>

    <!-- Overlay (for small screens) -->
    <div id="overlay"></div>

    <!-- Main Content -->
    <div class="content">
        <nav class="navbar navbar-light bg-white shadow-sm d-flex justify-content-between justify-content-md-end">
            <button id="toggleSidebar" class="btn d-md-none" style="background-color: #022213; color: white;">☰</button>
            <h4 class="navbar-brand">Users</h4>
        </nav>
        <div class="container-fluid px-4 py-3">
            <div class="">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-4">
                        <h4>Users List</h4>
                        <button class="btn" style="background-color: #022213; color: white;" data-toggle="modal"
                            data-target="#addUserModal">Add User</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="" style="background-color: #022213; color: white; font-weight:200;">
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Image</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result->num_rows > 0): ?>
                                    <?php while ($user = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($user['full_name']) ?></td>
                                            <td><?= htmlspecialchars($user['email']) ?></td>
                                            <td><?= htmlspecialchars($user['phone_number']) ?></td>
                                            <td>
                                                <img style="width: 30px; height: 30px;" src="get_image.php?id=<?= $user['id'] ?>"
                                                    alt="User Image">
                                            </td>



                                            <td>

                                                <!-- Update button with icon -->
                                                <button class="btn btn-sm" style="background-color: #2b6656; color: white"
                                                    data-toggle="modal" data-target="#updateUserModal"
                                                    data-id="<?= $user['id'] ?>" data-name="<?= $user['full_name'] ?>"
                                                    data-address="<?= $user['address'] ?>" data-username="<?= $user['username'] ?>"
                                                    data-phone_number="<?= $user['phone_number'] ?>"
                                                    data-image_location="<?= $user['image_location'] ?>"
                                                    data-email="<?= $user['email'] ?>">
                                                    <i class="fas fa-edit"></i>
                                                </button>

                                                <!-- Delete button with icon -->
                                                <a href="delete_user.php?id=<?= $user['id'] ?>" class="btn btn-sm"
                                                    style="background-color: #e55a54; color: white">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>


                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center">No users found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1" role="dialog" aria-labelledby="addUserModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="add.php" method="POST" enctype="multipart/form-data">
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
                            <input type="tel" class="form-control" id="phone_number" name="phone_number" required
                                pattern="^\d{10,15}$">
                        </div>

                        <div class="form-group">
                            <label for="password">Password:</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>

                        <!-- Image Upload Field -->
                        <div class="form-group">
                            <label for="image">Profile Image:</label>
                            <input type="file" class="form-control" id="image" name="image"
                                onchange="previewImage(event)" accept="image/*">
                            <img id="imagePreview" class="image-preview mt-2" src="#" alt="Image Preview"
                                style="display: none; width: 80px; height: 80px;">
                        </div>

                        <!-- Address Field -->
                        <div class="form-group">
                            <label for="address">Address:</label>
                            <textarea class="form-control" id="address" name="address" required></textarea>
                        </div>

                        <button type="submit" class="btn " style="background-color: #022213; color: white;">Add
                            User</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <!-- Update User Modal -->
    <div class="modal fade" id="updateUserModal" tabindex="-1" role="dialog" aria-labelledby="updateUserModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateUserModalLabel">Update User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="update.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" id="update_user_id" name="user_id">

                        <div class="form-group">
                            <label for="update_name">Full Name:</label>
                            <input type="text" class="form-control" id="update_name" name="full_name" required>
                        </div>

                        <div class="form-group">
                            <label for="update_username">Username:</label>
                            <input type="text" class="form-control" id="update_username" name="username" required>
                        </div>

                        <div class="form-group">
                            <label for="update_email">Email:</label>
                            <input type="email" class="form-control" id="update_email" name="email" required>
                        </div>

                        <div class="form-group">
                            <label for="update_phone_number">Phone Number:</label>
                            <input type="tel" class="form-control" id="update_phone_number" name="phone_number" required
                                pattern="^\d{10,15}$">
                        </div>

                        <!-- Image Upload Field -->
                        <div class="form-group">
                            <label for="update_image">Profile Image:</label>
                            <input type="file" class="form-control" id="update_image" name="image"
                                onchange="previewImage(event)" accept="image/*">
                            <img id="updateImagePreview" class="image-preview mt-2" src="#" alt="Image Preview"
                                style="display: none; width: 80px; height: 80px; display: none;">
                        </div>

                        <div class="form-group">
                            <label for="update_address">Address:</label>
                            <textarea class="form-control" id="update_address" name="address" required></textarea>
                        </div>

                        <button type="submit" class="btn" style="background-color: #022213; color: white;">Update
                            User</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <!-- Optional Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="script.js"></script>
    <script>
        // Show users section when 'Users' link is clicked
        document.getElementById('view-users').addEventListener('click', function () {
            document.getElementById('users-section').style.display = 'block';
        });

        // Populate the update modal with user data
        $('#updateUserModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); // Button that triggered the modal
            var userId = button.data('id');
            var name = button.data('name');
            var email = button.data('email');
            var address = button.data('address');
            var username = button.data('username');
            var phone_number = button.data('phone_number');
            var image_location = button.data('image_location');
            var email = button.data('email');

            var modal = $(this);
            modal.find('#update_user_id').val(userId);
            modal.find('#update_name').val(name);
            modal.find('#update_address').val(address);
            modal.find('#update_username').val(username);
            modal.find('#update_phone_number').val(phone_number);
            modal.find('#update_image_location').val(image_location);
            modal.find('#update_email').val(email);
        });

        function previewImage(event) {
            var file = event.target.files[0];
            var reader = new FileReader();

            reader.onload = function () {
                var imagePreview = event.target.id === 'image' ?
                    document.getElementById('imagePreview') :
                    document.getElementById('updateImagePreview');
                imagePreview.src = reader.result;
                imagePreview.style.display = "block";
            };

            if (file) {
                reader.readAsDataURL(file);
            }
        }


        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        const toggleButton = document.getElementById('toggleSidebar');

        toggleButton.addEventListener('click', () => {
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        });

        overlay.addEventListener('click', () => {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
        });


        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function (e) {
                let errors = [];

                const nameField = form.querySelector('[name="full_name"]');
                if (!/^[a-zA-Z\s]+$/.test(nameField.value)) {
                    errors.push("Full Name can only contain letters and spaces.");
                }

                const usernameField = form.querySelector('[name="username"]');
                if (!/^[a-zA-Z0-9]{4,20}$/.test(usernameField.value)) {
                    errors.push("Username must be 4-20 alphanumeric characters.");
                }

                const emailField = form.querySelector('[name="email"]');
                if (!/\S+@\S+\.\S+/.test(emailField.value)) {
                    errors.push("Invalid email format.");
                }

                const phoneField = form.querySelector('[name="phone_number"]');
                if (!/^\d{10,15}$/.test(phoneField.value)) {
                    errors.push("Phone number must be 10-15 digits.");
                }

                const passwordField = form.querySelector('[name="password"]');
                if (passwordField && passwordField.value.length > 0 && !/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/.test(passwordField.value)) {
                    errors.push("Password must be at least 8 characters, including letters and numbers.");
                }

                const addressField = form.querySelector('[name="address"]');
                if (!addressField.value.trim()) {
                    errors.push("Address cannot be empty.");
                }

                if (errors.length > 0) {
                    e.preventDefault(); // Prevent form submission
                    alert(errors.join('\n'));
                }
            });
        });

        // Success popup when Add/Update/Delete actions are successful
        <?php if (isset($_GET['message'])): ?>
            alert("<?php echo htmlspecialchars($_GET['message']); ?>");
        <?php endif; ?>
    </script>

</body>

</html>