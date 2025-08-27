<?php
require_once __DIR__ . '/../../backend/controllers/AttendanceController.php';
require_once __DIR__ . '/../../backend/models/Department.php';

// Capture filters from query string
$search = $_GET['search'] ?? '';
$department = $_GET['department'] ?? 'all';
$date = $_GET['date'] ?? null;

// Fetch data
$departments = Department::getAll();
$attendanceRecords = Attendance::getAll($search, $department, $date);
?>
<section class="attendance">
    <h2 class="stat-icon-1"><i class="fas fa-calendar-check"></i>  Attendance Records</h2>
    <p>View and manage daily attendance records for field workers</p>

    <div" class="attendance-controls">
        <div class="search-filter-row">
            <div class="search-bar">
                <input type="text" name="search" placeholder="Search by name" 
                       value="<?= htmlspecialchars($search) ?>">
            </div>
            
            <div class="filter-section">
                <select name="department">
                    <option value="all" <?= $department === 'all' ? 'selected' : '' ?>>All Departments</option>
                    <?php foreach ($departments as $dept): ?>
                        <option value="<?= htmlspecialchars($dept['name']) ?>" 
                            <?= $department === $dept['name'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($dept['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="date" name="date" value="<?= htmlspecialchars($date ?? date('Y-m-d')) ?>">
            </div>
        </div>
        <div class="controls">
            <button type="button" class="delete-btn" id="deleteSelectedBtn"><i class="fas fa-trash"></i> Delete Selected</button>
            <button type="submit" formaction="export_csv.php" formmethod="get" name="export" value="1" class="btn">
                Export CSV
            </button>
        </div>
    </div>

    <div class="table">
        <table>
            <thead>
                <tr>
                    <th><input type="checkbox" id="select-all"></th>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Department</th>
                    <th>Date</th>
                    <th>Check-In</th>
                    <th>Check-Out</th>
                    <th>Total Hours</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="attendance-table-body">
                <!-- Rows injected dynamically -->
            </tbody>

        </table>
    </div>
</section>

<script>
document.addEventListener("DOMContentLoaded", () => {
    // Initial load: all records
    loadAttendance();

    const searchInput = document.querySelector("input[name='search']");
    const deptSelect = document.querySelector("select[name='department']");
    const dateInput = document.querySelector("input[name='date']");

    // Live filters
    [searchInput, deptSelect, dateInput].forEach(el => {
        el.addEventListener("change", () => applyFilters());
        el.addEventListener("keyup", () => applyFilters()); // for search input
    });

    // Select all checkboxes
    document.getElementById("select-all").addEventListener("change", function() {
        const checkboxes = document.querySelectorAll(".record-checkbox");
        checkboxes.forEach(cb => cb.checked = this.checked);
    });

    // Bulk delete
    document.getElementById("delete-selected").addEventListener("click", function() {
        const selected = Array.from(document.querySelectorAll(".record-checkbox:checked")).map(cb => cb.value);
        if (!selected.length) return alert("No records selected.");
        if (!confirm("Are you sure you want to delete selected records?")) return;

        fetch("delete_many.php", {
            method: "POST",
            headers: {"Content-Type": "application/json"},
            body: JSON.stringify({ids: selected})
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) loadAttendance(getCurrentFilters());
            else alert("Failed to delete records.");
        });
    });
});

// Collect current filter values
function getCurrentFilters() {
    return {
        search: document.querySelector("input[name='search']").value,
        department: document.querySelector("select[name='department']").value,
        date: document.querySelector("input[name='date']").value
    };
}

// Apply current filters
function applyFilters() {
    loadAttendance(getCurrentFilters());
}

// Load attendance from API
function loadAttendance(filters = {}) {
    const params = new URLSearchParams(filters).toString();
    fetch(`../../backend/api/attendance_api.php?action=getAll&${params}`)
        .then(res => res.json())
        .then(data => {
            const tbody = document.getElementById("attendance-table-body");
            tbody.innerHTML = "";

            if (!data || !data.length) {
                tbody.innerHTML = `<tr><td colspan="10" class="no-records">No attendance records found.</td></tr>`;
                return;
            }

            data.forEach(row => {
                const tr = document.createElement("tr");
                tr.innerHTML = `
                    <td><input type="checkbox" class="record-checkbox" value="${row.id}"></td>
                    <td>${row.id}</td>
                    <td>${row.full_name}</td>
                    <td>${row.department}</td>
                    <td>${row.date}</td>
                    <td>${row.check_in}</td>
                    <td>${row.check_out ?? "-"}</td>
                    <td>${row.total_hours ?? "-"}</td>
                    <td>${row.status}</td>
                    <td>
                        <a class="action-btn delete" href="delete_attendance.php?id=${row.id}" onclick="return confirm('Delete this record?')">
                            <i style="color: rgba(122, 0, 0, 1);" class="fas fa-trash"></i>
                        </a>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        })
        .catch(err => console.error("Error loading attendance:", err));
}
</script>