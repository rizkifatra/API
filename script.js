const API_BASE_URL = "http://localhost/api"; // Your backend server URL

// Function to handle login
function login(event) {
  event.preventDefault();
  const username = document.getElementById("username").value;
  const password = document.getElementById("password").value;

  fetch(`${API_BASE_URL}/login.php`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({ user: username, pass: password }),
  })
    .then((res) => {
      if (!res.ok) {
        throw new Error(`HTTP error! status: ${res.status}`);
      }
      return res.json();
    })
    .then((data) => {
      window.location.href = "home.html";
    })
    .catch((error) => {
      console.error("Login failed:", error);
      alert("Login failed");
    });
}

// Fetch API Data Example
if (document.getElementById("dataList")) {
  fetch(`${API_BASE_URL}/data.php`)
    .then((res) => {
      if (!res.ok) {
        throw new Error(`HTTP error! status: ${res.status}`);
      }
      return res.json();
    })
    .then((data) => {
      const list = document.getElementById("dataList");
      data.forEach((item) => {
        const li = document.createElement("li");
        li.textContent = JSON.stringify(item);
        list.appendChild(li);
      });
    })
    .catch((error) => {
      console.error("Failed to fetch API data:", error);
      alert("Failed to fetch API data");
    });
}
