const API_BASE_URL = "http://localhost"; // Your backend server URL

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
    body: JSON.stringify({ username, password }),
  })
    .then((res) => {
      if (!res.ok) {
        throw new Error(`HTTP error! status: ${res.status}`);
      }
      return res.json();
    })
    .then((data) => {
      localStorage.setItem("token", data.token);
      window.location.href = "dashboard.html";
    })
    .catch((error) => {
      console.error("Login failed:", error);
      alert("Login failed");
    });
}

// Fetch API Data Example
if (document.getElementById("dataList")) {
  const token = localStorage.getItem("token");

  if (!token) {
    window.location.href = "index.html"; // Redirect to login if not authenticated
  }

  fetch(`${API_BASE_URL}/data.php`, {
    headers: {
      Authorization: `Bearer ${token}`,
    },
  })
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

// Event listener for login form submission
document.getElementById("loginForm").addEventListener("submit", login);
