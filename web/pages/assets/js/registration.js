// login.js

// Example function for handling registration form submission
document.getElementById('registrationForm').addEventListener('submit', function(event) {
    event.preventDefault();
    // Retrieve form values
    const username = document.getElementById('username').value;
    const email = document.getElementById('email').value;
    const cellphone = document.getElementById('cellphone').value;
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
  
    // Validate passwords match
    if (password !== confirmPassword) {
      // Display error message or perform necessary actions for mismatched passwords
      return;
    }
  
    // Perform further validation as needed
  
    // Example AJAX request using Fetch API (to be connected to your backend)
    fetch('/register', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ username, email, cellphone, password })
    })
    .then(response => {
      if (response.ok) {
        // Redirect to a success page or perform actions upon successful registration
        window.location.href = 'registration-success.html';
      } else {
        // Handle registration failure
        // Display error message or perform necessary actions
      }
    })
    .catch(error => console.error('Error:', error));
  });
  