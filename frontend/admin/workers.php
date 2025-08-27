<section class="field-workers">
    <h2 class="stat-icon-1"><i class="fas fa-users-cog"></i> Field Workers Management</h2>
    <p>Manage and view details of field workers</p>

    <div class="controls-container">
        <div class="search-bar">
            <input type="text" id="searchInput" placeholder="Search by name, department, or email">
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
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="workerTableBody">
                <!-- Dynamic rows will be injected here -->
            </tbody>
        </table>
        <p class="no-records" style="display:none;">No field workers found.</p>
    </div>
</section>

<script>
document.addEventListener("DOMContentLoaded", function() {
    fetchWorkers(); // Load all workers initially

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
        const selectedIds = Array.from(document.querySelectorAll(".selectWorker:checked"))
            .map(cb => cb.value);
        if (!selectedIds.length) {
            alert("Please select workers to delete.");
            return;
        }
        if (!confirm("Are you sure you want to delete selected workers?")) return;

        Promise.all(selectedIds.map(id => deleteWorker(id, false)))
            .then(() => fetchWorkers());
    });
});

// Fetch workers from API
function fetchWorkers(search = "") {
    const action = search ? `search&q=${encodeURIComponent(search)}` : "getAll";
    fetch(`/ttcl_proj/backend/api/fieldworker_api.php?action=${action}`)
        .then(res => res.json())
        .then(data => {
            const tbody = document.getElementById("workerTableBody");
            tbody.innerHTML = "";
            const noRecords = document.querySelector(".no-records");

            if (!data || !data.length) {
                noRecords.style.display = "block";
                return;
            }

            noRecords.style.display = "none";

            data.forEach(worker => {
                const tr = document.createElement("tr");
                tr.innerHTML = `
                    <td><input type="checkbox" class="selectWorker" value="${worker.user_id}" onchange="updateDeleteCount()"></td>
                    <td>${worker.full_name}</td>
                    <td>${worker.department_name || "N/A"}</td>
                    <td>${worker.college_name || "N/A"}</td>
                    <td>${worker.email || "N/A"}</td>
                    <td>${worker.start_date} to ${worker.end_date}</td>
                    <td><button class="group-btn">${worker.group_name || "N/A"}</button></td>
                    <td>${worker.role || "N/A"}</td>
                    <td>
                        <div class="action-buttons">
                            <button class="action-btn" onclick="editWorker(${worker.user_id})"><i class="fas fa-edit"></i></button>
                            <button class="action-btn delete" onclick="deleteWorker(${worker.user_id})"><i style="color: rgba(122, 0, 0, 1);" class="fas fa-trash"></i></button>
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
        `<i class="fas fa-trash"></i> Delete Selected (${count})`;
}

// Delete worker
function deleteWorker(id, showAlert = true) {
    return fetch(`/ttcl_proj/backend/api/fieldworker_api.php?action=delete&id=${id}`)
        .then(res => res.json())
        .then(resp => {
            if (resp.success && showAlert) alert("Worker deleted successfully.");
            return resp.success;
        })
        .catch(err => console.error("Delete error:", err));
}

// Edit worker placeholder
function editWorker(id) {
    alert("Edit worker profile with ID: " + id);
}
</script>