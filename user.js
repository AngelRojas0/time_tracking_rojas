let startTime = null;
let timerInterval = null;
let currentEntryId = null;

function startTimer() {
  fetch("start_timer.php")
    .then(res => res.json())
    .then(data => {
      currentEntryId = data.entry_id;
      startTime = Date.now();
      timerInterval = setInterval(updateTimer, 1000);
    });
}

function stopTimer() {
  clearInterval(timerInterval);

  fetch("stop_timer.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `entry_id=${currentEntryId}`
  })
  .then(res => res.json())
  .then(() => {
    alert("✅ Time saved successfully");
  });
}

function updateTimer() {
  const elapsed = Math.floor((Date.now() - startTime) / 1000);
  document.getElementById("timer").textContent = formatTime(elapsed);
}

function formatTime(sec) {
  const h = String(Math.floor(sec / 3600)).padStart(2, "0");
  const m = String(Math.floor((sec % 3600) / 60)).padStart(2, "0");
  const s = String(sec % 60).padStart(2, "0");
  return `${h}:${m}:${s}`;
}

function logout() {
  window.location.href = "index.html";
}
