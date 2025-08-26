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
    <h2>Attendance Records</h2>
    <p>View and manage daily attendance records for field workers</p>

    <form method="GET" class="attendance-controls">
        <div class="search-filter-row">
            <div class="search-bar">
                <input type="text" name="search" placeholder="Search by name or department..." 
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
                <input type="date" name="date" value="<?= htmlspecialchars($date ?? '') ?>">
            </div>
        </div>
        <div class="controls">
            <button type="submit">Filter</button>
            <button type="button" id="delete-selected">Delete Selected</button>
            <button type="submit" formaction="export_csv.php" formmethod="get" name="export" value="1" class="btn">
                Export CSV
            </button>
            <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
            <input type="hidden" name="department" value="<?= htmlspecialchars($department) ?>">
            <input type="hidden" name="date" value="<?= htmlspecialchars($date) ?>">
        </div>
    </form>

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
            <tbody>
            <?php if (!empty($attendanceRecords)): ?>
                <?php foreach ($attendanceRecords as $row): ?>
                    <tr>
                        <td><input type="checkbox" class="record-checkbox" value="<?= $row['id'] ?>"></td>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['full_name']) ?></td>
                        <td><?= htmlspecialchars($row['department']) ?></td>
                        <td><?= htmlspecialchars($row['date']) ?></td>
                        <td><?= htmlspecialchars($row['check_in']) ?></td>
                        <td><?= htmlspecialchars($row['check_out']) ?></td>
                        <td><?= $row['total_hours'] !== null ? $row['total_hours'] : '-' ?></td>
                        <td><?= htmlspecialchars($row['status']) ?></td>
                        <td>
                            <a href="delete_attendance.php?id=<?= $row['id'] ?>" onclick="return confirm('Delete this record?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="10"><p class="no-records">No attendance records found for the selected criteria.</p></td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<script>
// Select all checkboxes
document.getElementById("select-all").addEventListener("change", function() {
    const checkboxes = document.querySelectorAll(".record-checkbox");
    checkboxes.forEach(cb => cb.checked = this.checked);
});

// Bulk delete
document.getElementById("delete-selected").addEventListener("click", function() {
    const selected = Array.from(document.querySelectorAll(".record-checkbox:checked")).map(cb => cb.value);
    if (selected.length === 0) {
        alert("No records selected.");
        return;
    }

    if (!confirm("Are you sure you want to delete selected records?")) return;

    fetch("delete_many.php", {
        method: "POST",
        headers: {"Content-Type": "application/json"},
        body: JSON.stringify({ids: selected})
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) location.reload();
        else alert("Failed to delete records.");
    });
});
</script>