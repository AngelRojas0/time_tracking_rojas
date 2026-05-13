let startTime;
let elapsedTime = 0;
let timerInterval;
let idleTimeout;

//IDLE DETECTION CONFIGURATION
const IDLE_LIMIT = 60000; 

function resetIdleTimer() {
    clearTimeout(idleTimeout);
    
    if (timerInterval) {
        idleTimeout = setTimeout(recordIdleEvent, IDLE_LIMIT);
    }
}

// Event listeners for activity
window.onmousemove = resetIdleTimer;
window.onkeydown = resetIdleTimer;

function recordIdleEvent() {
    console.log("User detected as idle. Recording to database...");
    const formData = new FormData();
    formData.append('idle_duration', 60); 

    fetch('record_idle.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => console.log("Idle event saved:", data))
    .catch(err => console.error("Idle record failed:", err));

   
    resetIdleTimer();
}

document.addEventListener("DOMContentLoaded", () => {
    fetchDatabaseData();
});

function startTimer() {
    if (timerInterval) return;
    startTime = Date.now() - elapsedTime;
    timerInterval = setInterval(() => {
        elapsedTime = Date.now() - startTime;
        document.getElementById("timer").innerText = timeToString(elapsedTime);
    }, 1000);
    
    resetIdleTimer(); 
}

function stopTimer() {
    if (!timerInterval) return;

    const sessionTime = timeToString(elapsedTime);
    
    clearInterval(timerInterval);
    timerInterval = null;
    clearTimeout(idleTimeout); 

    const formData = new FormData();
    formData.append('duration', sessionTime);

    fetch('save_time.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === "success") {
            alert("Session saved to database!");
            elapsedTime = 0;
            document.getElementById("timer").innerText = "00:00:00";
            location.reload();
        } else {
            alert("Error: " + data.message);
        }
    })
    .catch(error => {
        console.error("Fetch error:", error);
    });
}

function fetchDatabaseData() {
    fetch('get_time_logs.php')
        .then(res => res.json())
        .then(data => {
            const logsBody = document.getElementById("logsBody");
            const reportTotal = document.getElementById("reportTotal");
            const dashboardTotal = document.getElementById("total");

            let grandTotalSeconds = 0;

            if (logsBody) logsBody.innerHTML = ""; 

            data.forEach(log => {
                const secs = parseInt(log.duration_seconds);
                grandTotalSeconds += secs;

                if (logsBody) {
                    const row = `<tr>
                        <td>${log.created_at}</td>
                        <td>${secondsToHHMMSS(secs)}</td>
                        <td style="color: green; font-weight: bold;">Saved</td>
                    </tr>`;
                    logsBody.innerHTML += row;
                }
            });

            if (reportTotal) reportTotal.innerText = secondsToHHMMSS(grandTotalSeconds);
            if (dashboardTotal && data.length > 0) {
                dashboardTotal.innerText = secondsToHHMMSS(data[0].duration_seconds);
            }
        });
}

function timeToString(time) {
    let hh = Math.floor(time / 3600000);
    let mm = Math.floor((time % 3600000) / 60000);
    let ss = Math.floor((time % 60000) / 1000);
    return `${hh.toString().padStart(2, "0")}:${mm.toString().padStart(2, "0")}:${ss.toString().padStart(2, "0")}`;
}

function secondsToHHMMSS(totalSeconds) {
    let h = Math.floor(totalSeconds / 3600);
    let m = Math.floor((totalSeconds % 3600) / 60);
    let s = totalSeconds % 60;
    return `${h.toString().padStart(2, "0")}:${m.toString().padStart(2, "0")}:${s.toString().padStart(2, "0")}`;
}

function logout() {
    window.location.href = 'index.html';
}