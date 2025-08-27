<section class="department-management">
    <h2 class="stat-icon-1"><i class="fas fa-building">  Department Management </i></h2>
    <p>Manage departments and their check-in/check-out time limit</p>
    <div class="controls-container">
        <div class="controls">
            <button class="add-btn"><i class="fas fa-plus"></i> Add New Department</button>
        </div>
    </div>
    <div class="table">
        <table>
            <thead>
                <tr>
                    <th><input type="checkbox" class="record-checkbox" id="selectAll"></th>
                    <th>ID</th>
                    <th>Department Name</th>
                    <th>Check-in Limit</th>
                    <th>Check-out Limit</th>
                    <th>Workers</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="departments-table-body">
                <!-- Rows will be injected dynamically -->
            </tbody>
        </table>
        <p class="no-records" style="display: none;">No departments found for the selected criteria.</p>
    </div>
</section>

<script>
document.addEventListener("DOMContentLoaded", function() {
    loadDepartments();

    function loadDepartments() {
        fetch("http://localhost/ttcl_proj/backend/api/department_api.php?action=getAll")
            .then(response => response.json())
            .then(data => {
                const tbody = document.getElementById("departments-table-body");
                const noRecords = document.querySelector(".no-records");
                tbody.innerHTML = "";

                if (data.length === 0) {
                    noRecords.style.display = "block";
                    return;
                }

                noRecords.style.display = "none";

                data.forEach(dept => {
                    const row = document.createElement("tr");
                    row.innerHTML = `
                        <td><input type="checkbox" class="record-checkbox" value="${dept.id}"></td>
                        <td>${dept.id}</td>
                        <td>${dept.name}</td>
                        <td>🕝 ${dept.check_in_limit}</td>
                        <td>🕝 ${dept.check_out_limit}</td>
                        <td>${dept.worker_count ?? 0} workers</td>
                        <td>
                            <div class="action-buttons">
                                <button class="action-btn edit" data-id="${dept.id}"><i class="fas fa-edit"></i></button>
                                <button class="action-btn delete" data-id="${dept.id}"><i style="color: rgba(122, 0, 0, 1);" class="fas fa-trash"></i></button>
                            </div>
                        </td>
                    `;
                    tbody.appendChild(row);
                });

                
                document.querySelectorAll(".action-btn.delete").forEach(btn => {
                    btn.addEventListener("click", function() {
                        const deptId = this.dataset.id;
                        if (confirm("Are you sure you want to delete this department?")) {
                            deleteDepartment(deptId);
                        }
                    });
                });
            })
            .catch(err => console.error("Error loading departments:", err));
    }

    function deleteDepartment(id) {
        fetch("http://localhost/ttcl_proj/backend/api/department_api.php?action=delete", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "id=" + encodeURIComponent(id)
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                alert("Department deleted successfully!");
                loadDepartments(); // Refresh the table
            } else {
                alert("Failed to delete department: " + (result.message || "Unknown error"));
            }
        })
        .catch(err => console.error("Delete error:", err));
    }
});
</script>
