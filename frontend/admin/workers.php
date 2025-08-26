<section class="field-workers">
    <h2>Field Workers Management <i class="fas fa-users-cog"></i></h2>
    <p>Manage and view details of field workers</p>

    <div class="controls-container">
        <div class="search-bar">
            <input type="text" id="searchInput" placeholder="Search by name, department or email">
        </div>
        <div class="controls">
            <button class="delete-btn" id="deleteSelectedBtn"><i class="fas fa-trash"></i> Delete Selected (0)</button>
            <button class="add-btn"><i class="fas fa-plus"></i> Add New Worker</button>
        </div>
    </div>

    <div class="table">
        <table>
            <thead>
                <tr>
                    <th><input type="checkbox" id="selectAll"></th>
                    <th>Full Name</th>
                    <th>Department</th>
                    <th>College/University</th>
                    <th>Email</th>
                    <th>Period</th>
                    <th>Group</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="workerTableBody">
                <!-- Dynamic rows will be injected here -->
            </tbody>
        </table>
        <p class="no-records" style="display:none;">No field workers found for the selected criteria.</p>
    </div>
</section>

<script>
document.addEventListener("DOMContentLoaded", function() {
    fetchWorkers();

    // Live search
    document.getElementById("searchInput").addEventListener("keyup", function() {
        fetchWorkers(this.value);
    });

    // Select all checkbox
    document.getElementById("selectAll").addEventListener("change", function() {
        const checkboxes = document.querySelectorAll(".selectWorker");
        checkboxes.forEach(cb => cb.checked = this.checked);
        updateDeleteCount();
    });

    // Delete selected
    document.getElementById("deleteSelectedBtn").addEventListener("click", function() {
        const selected = Array.from(document.querySelectorAll(".selectWorker:checked"))
            .map(cb => cb.value);
        if (selected.length === 0) {
            alert("Please select workers to delete.");
            return;
        }
        if (!confirm("Are you sure you want to delete selected workers?")) return;

        selected.forEach(id => deleteWorker(id));
    });
});

// Fetch workers from backend
function fetchWorkers(search = "") {
    fetch(controllers/FieldWorkerController.php?action=list&search=${encodeURIComponent(search)})
        .then(res => res.json())
        .then(data => {
            const tbody = document.getElementById("workerTableBody");
            tbody.innerHTML = "";

            if (data.length === 0) {
                document.querySelector(".no-records").style.display = "block";
                return;
            }

            document.querySelector(".no-records").style.display = "none";

            data.forEach(worker => {
                const tr = document.createElement("tr");
                tr.innerHTML = `
                    <td><input type="checkbox" class="selectWorker" value="${worker.user_id}" onchange="updateDeleteCount()"></td>
                    <td>${worker.full_name}</td>
                    <td>${worker.department_name}</td>
                    <td>${worker.college}</td>
                    <td>${worker.email}</td>
                    <td>${worker.start_date} to ${worker.end_date}</td>
                    <td><button class="group-btn">${worker.group_name || "N/A"}</button></td>
                    <td>
                        <button class="status-btn ${worker.status === 'active' ? 'active' : 'inactive'}">
                            ${worker.status === 'active' ? 'Active <i class="fas fa-check-circle"></i>' : 'Inactive <i class="fas fa-times-circle"></i>'}
                        </button>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <button class="action-btn" onclick="editWorker(${worker.user_id})"><i class="fas fa-edit"></i></button>
                            <button class="action-btn delete" onclick="deleteWorker(${worker.user_id})"><i class="fas fa-trash"></i></button>
                        </div>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        })
        .catch(err => console.error("Error fetching workers:", err));
}

// Update delete button count
function updateDeleteCount() {
    const count = document.querySelectorAll(".selectWorker:checked").length;
    document.getElementById("deleteSelectedBtn").innerHTML =
        <i class="fas fa-trash"></i> Delete Selected (${count});
}

// Delete worker
function deleteWorker(id) {
    fetch(controllers/FieldWorkerController.php?action=delete&id=${id})
        .then(res => res.json())
        .then(resp => {
            if (resp.success) {
                alert("Worker deleted successfully.");
                fetchWorkers();
            } else {
                alert("Failed to delete worker.");
            }
        })
        .catch(err => console.error("Delete error:", err));
}

// Edit worker (placeholder)
function editWorker(id) {
    alert("Edit worker profile with ID: " + id);
    // You can redirect to edit form or open modal
}
</script>