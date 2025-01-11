const API_BASE_URL = "http://localhost/api/register"; // Your backend server URL

// Function to handle registration
function register(event) {
  event.preventDefault();
  const username = document.getElementById("username").value;
  const password = document.getElementById("password").value;

  fetch(`${API_BASE_URL}/register.php`, {
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
      alert("Registration successful");
      window.location.href = "../index.html";
    })
    .catch((error) => {
      console.error("Registration failed:", error);
      document.getElementById("registerError").textContent =
        "Registration failed";
    });
}

// Event listener for form submission
document.getElementById("registerForm").addEventListener("submit", register);
