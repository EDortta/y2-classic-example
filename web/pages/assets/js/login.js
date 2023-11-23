
document.getElementById('loginForm').addEventListener('submit', function(event) {
    event.preventDefault();
    // Retrieve username and password
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
  
    // Example AJAX request using Fetch API (you'll need to handle this based on your backend)
    fetch('/login', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ username, password })
    })
    .then(response => {
      if (response.ok) {
        // Redirect to admin page upon successful login
        window.location.href = 'admin.html';
      } else {
        // Handle invalid login
        // Display error message or perform necessary actions
      }
    })
    .catch(error => console.error('Error:', error));
  });
  