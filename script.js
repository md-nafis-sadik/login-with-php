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