function displayMessage(message, type) {
  var msg=y$('loginAlert');
  msg.innerHTML=message;
  msg.style.display="block";

  if (type=="error") {
    msg.classList.add("alert-danger");
    msg.classList.remove("alert-success");
  } else {
    msg.classList.add("alert-success");
    msg.classList.remove("alert-danger");
  }

  setTimeout(function() {
    msg.style.display="none";
  }, 3000);
}


function processResponse(response) {
  if (response.ok) {
    response.json().then(data => {
      console.log(data);
      if (typeof data.sessionToken=="string") {
        sessionToken=data.sessionToken;
        window.location.href="admin";
      } else {
        displayMessage('Invalid credentials', 'error');
      }
    }).catch(error => {
      displayMessage('Error: ' + error.message, 'error');
      console.error('Error:', error);
    });
  } else {
    switch (response.status) {
      case 401:
        displayMessage('Invalid credentials', 'error');
        break;
      case 500:
        displayMessage('Server error', 'error');
        break;
      default:
        displayMessage('Unknown error', 'error');
        break;
    }
  }
}

function processError(error) {
  console.error('Error:', error)
}

function loginEventHandler(event) {
  event.preventDefault();

  const username = y$('username').value;
  const password = y$('password').value;

  fetch('/login', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json'
    },
    body: JSON.stringify({ username, password, sessionToken })
  })
    .then(processResponse)
    .catch(processError);
}

function setBtnLoginEvent() {
  yDom.addEvent("btnLogin", "click", loginEventHandler);
}

addOnLoadManager(setBtnLoginEvent);