
        function validateForm() {
            var fullName = document.getElementById('full_name').value;
            var email = document.getElementById('email').value;
            var username = document.getElementById('username').value;
            var password = document.getElementById('password').value;
            var phoneNumber = document.getElementById('phone_number').value;

            if (fullName.trim() === "") {
                alert("Full Name is required.");
                return false;
            }

            var emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
            if (!emailRegex.test(email)) {
                alert("Invalid email format.");
                return false;
            }

            var usernameRegex = /^[a-zA-Z0-9]*$/;
            if (!usernameRegex.test(username)) {
                alert("Username can only contain letters and numbers.");
                return false;
            }

            if (password.length < 8) {
                alert("Password must be at least 8 characters long.");
                return false;
            }

            var phoneRegex = /^\d{11}$/;
            if (!phoneRegex.test(phoneNumber)) {
                alert("Phone number must be 10 digits long.");
                return false;
            }

            return true; 
        }
