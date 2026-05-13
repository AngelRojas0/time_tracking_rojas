document.addEventListener("DOMContentLoaded", () => {
    // Theme sync
    const savedTheme = localStorage.getItem("theme");
    if (savedTheme === "dark") {
        document.documentElement.classList.add("dark-mode");
        document.body.classList.add("dark-mode");
    }

    // Page-specific initialization
    if (document.getElementById("adminLogsBody")) {
        loadAllLogs();
        const searchInput = document.getElementById("logSearch");
        if (searchInput) searchInput.addEventListener("keyup", filterLogs);
    }

    if (document.getElementById("sessionChart")) {
        loadSessionGraph();
    }
});

/**
 * Fetches data, calculates dashboard stats, and sorts the table
 */
function loadAllLogs() {
    const adminLogsBody = document.getElementById("adminLogsBody");
    const totalUsersText = document.getElementById("totalUsers");
    const totalSessionsText = document.getElementById("totalSessions");
    const globalTimeText = document.getElementById("globalTime");
    
    const sortDropdown = document.getElementById("sortOption");
    const sortValue = sortDropdown ? sortDropdown.value : 'date_desc';

    fetch('get_time_logs.php')
        .then(response => response.json())
        .then(data => {
            if (!data || data.length === 0) {
                if (adminLogsBody) adminLogsBody.innerHTML = "<tr><td colspan='5'>No logs found.</td></tr>";
                return;
            }

            // 1. ADVANCED SORTING LOGIC
            data.sort((a, b) => {
                const dateA = new Date(a.created_at);
                const dateB = new Date(b.created_at);
                const durA = parseInt(a.duration_seconds) || 0;
                const durB = parseInt(b.duration_seconds) || 0;

                switch(sortValue) {
                    case 'date_asc': return dateA - dateB;
                    case 'date_desc': return dateB - dateA;
                    case 'dur_asc': return durA - durB;
                    case 'dur_desc': return durB - durA;
                    default: return dateB - dateA;
                }
            });

            // 2. STATS & RENDERING
            let totalSeconds = 0;
            let uniqueUsers = new Set();
            if (adminLogsBody) adminLogsBody.innerHTML = ""; 

            data.forEach(log => {
                const duration = parseInt(log.duration_seconds) || 0;
                totalSeconds += duration;
                if (log.user_id) uniqueUsers.add(log.user_id);

                if (adminLogsBody) {
                    const userEmail = (log.email && log.email.trim() !== "") ? log.email : "Not Provided";
                    const row = `
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 12px;">${log.user_id}</td>
                            <td style="padding: 12px; font-weight: bold;">${log.username}</td>
                            <td style="padding: 12px; color: #7f8c8d;">${userEmail}</td>
                            <td style="padding: 12px;">${new Date(log.created_at).toLocaleString()}</td>
                            <td style="padding: 12px; font-weight: bold;">${formatSeconds(duration)}</td>
                        </tr>`;
                    adminLogsBody.innerHTML += row;
                }
            });

            // 3. UPDATE UI COUNTERS
            if (totalUsersText) totalUsersText.innerText = uniqueUsers.size;
            if (totalSessionsText) totalSessionsText.innerText = data.length;
            if (globalTimeText) globalTimeText.innerText = formatSeconds(totalSeconds);
            
            filterLogs(); // Keep search active after sort
        })
        .catch(err => console.error("Error loading admin data:", err));
}

/**
 * Live Table Filter
 */
function filterLogs() {
    const searchInput = document.getElementById("logSearch");
    if (!searchInput) return;

    const query = searchInput.value.toLowerCase();
    const rows = document.querySelectorAll("#adminLogsBody tr");

    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(query) ? "" : "none";
    });
}

/**
 * Time Formatter
 */
function formatSeconds(totalSeconds) {
    const h = Math.floor(totalSeconds / 3600);
    const m = Math.floor((totalSeconds % 3600) / 60);
    const s = totalSeconds % 60;
    return `${h.toString().padStart(2, "0")}:${m.toString().padStart(2, "0")}:${s.toString().padStart(2, "0")}`;
}

/**
 * Chart.js Integration
 */
function loadSessionGraph() {
    const canvas = document.getElementById('sessionChart');
    if (!canvas) return;

    fetch('get_session_stats.php')
        .then(response => response.json())
        .then(data => {
            const labels = data.map(item => item.entry_date);
            const counts = data.map(item => item.session_count);

            new Chart(canvas.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Sessions',
                        data: counts,
                        backgroundColor: '#3498db',
                        borderRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
                }
            });
        });
}

function logout() {
    sessionStorage.clear();
    window.location.href = 'index.html';
}

function printLogs() {
    window.print();
}