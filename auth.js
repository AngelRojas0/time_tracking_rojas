function login(e) {
  e.preventDefault();

  const username = loginUsername.value.trim();
  const password = loginPassword.value;

  if (!users[username] || users[username].password !== password) {
    alert("Invalid credentials");
    return;
  }

  localStorage.setItem("currentUser", username);
  localStorage.setItem("role", users[username].role);

  if (users[username].role === "admin") {
    location.href = "admin-dashboard.html";
  } else {
    location.href = "user-dashboard.html";
  }
}
