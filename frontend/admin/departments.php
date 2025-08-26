<section class="department-management">
    <h2>Department Management <i class="fas fa-building"></i></h2>
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
                    <th><span class="circle-icon" title="Select all"><i class="far fa-circle"></i></span></th>
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
    fetch("controllers/DepartmentController.php?action=view")
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
                    <td><span class="circle-icon" title="Select row"><i class="far fa-circle"></i></span></td>
                    <td>${dept.id}</td>
                    <td>${dept.name}</td>
                    <td><i class="fas fa-clock"></i> ${dept.check_in_limit}</td>
                    <td><i class="fas fa-clock"></i> ${dept.check_out_limit}</td>
                    <td>${dept.worker_count} workers</td>
                    <td>
                        <div class="action-buttons">
                            <button class="action-btn edit" data-id="${dept.id}"><i class="fas fa-edit"></i></button>
                            <button class="action-btn delete" data-id="${dept.id}"><i class="fas fa-trash"></i></button>
                        </div>
                    </td>
                `;
                tbody.appendChild(row);
            });
        })
        .catch(err => console.error("Error loading departments:", err));
});
</script>
