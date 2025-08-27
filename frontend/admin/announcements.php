<section class="announcements-management">
    <h2 class="stat-icon-1"><i class="fas fa-bullhorn"></i> Announcements Management</h2>
    <p>Create and manage announcements for field workers by department</p>

    <div class="controls-container">
        <div class="announcements-controls">
            <div class="filter-section">
                <select id="announcement-department">
                    <option value="all">All Departments</option>
                </select>
                <input type="date" id="announcement-date">
            </div>
        </div>
        <div class="controls">
            <button type="button" id="delete-selected">Delete Selected</button>
            <button class="add-btn" onclick="window.location.href='add_announcement.php'">
                <i class="fas fa-plus"></i> Add New Announcement
            </button>
        </div>
    </div>

    <div class="table">
        <table>
            <thead>
                <tr>
                    <th><input type="checkbox" class="record-checkbox" id="selectAll"></th>
                    <th>ID</th>
                    <th>Department</th>
                    <th>Title</th>
                    <th>Message</th>
                    <th>Display Date</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="announcement-table-body"></tbody>
        </table>
    </div>
</section>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const departmentSelect = document.getElementById("announcement-department");
    const dateInput = document.getElementById("announcement-date");
    const tableBody = document.getElementById("announcement-table-body");

    // Create Clear Filters button dynamically
    const clearBtn = document.createElement("button");
    clearBtn.textContent = "Clear Filters";
    clearBtn.type = "button";
    clearBtn.className = "add-btn";
    clearBtn.style.marginLeft = "10px";
    clearBtn.addEventListener("click", function () {
        departmentSelect.value = "all";
        dateInput.value = "";
        loadAnnouncements();
    });
    document.querySelector(".filter-section").appendChild(clearBtn);

    // Fetch and populate departments dynamically
    function loadDepartments() {
        fetch("../../backend/api/department_api.php?action=list")
            .then(res => res.json())
            .then(data => {
                data.forEach(dept => {
                    const opt = document.createElement("option");
                    opt.value = dept.id;
                    opt.textContent = dept.name;
                    departmentSelect.appendChild(opt);
                });
            });
    }

    // Fetch announcements with filters
    function loadAnnouncements() {
        const department_id = departmentSelect.value !== "all" ? departmentSelect.value : "";
        const display_date = dateInput.value ? dateInput.value : "";

        const url = `../../backend/api/announcement_api.php?action=list&department_id=${department_id}&display_date=${display_date}`;
        console.log("Fetching:", url); // Debug

        fetch(url)
            .then(res => res.json())
            .then(data => {
                console.log("API Response:", data); // Debug
                tableBody.innerHTML = "";
                if (data.length === 0) {
                    tableBody.innerHTML = "<tr><td colspan='8'>No announcements found</td></tr>";
                    return;
                }

                data.forEach(item => {
                    let row = `
                        <tr>
                            <td><input type="checkbox" class="record-checkbox" data-id="${item.id}"></td>
                            <td>${item.id}</td>
                            <td>${item.department_name}</td>
                            <td>${item.title}</td>
                            <td>${item.message}</td>
                            <td>${item.display_date}</td>
                            <td>${item.created_at}</td>
                            <td>
                                <button class="action-btn edit" onclick="editAnnouncement(${item.id})"><i class="fas fa-edit"></i></button>
                                <button class="action-btn delete" onclick="deleteAnnouncement(${item.id})"><i style="color: rgba(122, 0, 0, 1);" class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                    `;
                    tableBody.insertAdjacentHTML("beforeend", row);
                });
            });
    }

    // Hook filters
    departmentSelect.addEventListener("change", loadAnnouncements);
    dateInput.addEventListener("change", loadAnnouncements);

    // Initial load
    loadDepartments();
    loadAnnouncements();
});

// Select all checkboxes
    document.getElementById("selectAll").addEventListener("change", function() {
        const checkboxes = document.querySelectorAll(".record-checkbox");
        checkboxes.forEach(cb => cb.checked = this.checked);
    });

// Delete function
function deleteAnnouncement(id) {
    if (!confirm("Are you sure you want to delete this announcement?")) return;
    fetch(`../../backend/api/announcement_api.php?action=delete&id=${id}`)
        .then(res => res.json())
        .then(resp => {
            if (resp.success) {
                alert("Deleted successfully!");
                location.reload();
            } else {
                alert("Failed to delete");
            }
        });
}

// Edit placeholder
function editAnnouncement(id) {
    window.location.href = `edit_announcement.php?id=${id}`;
}
</script>


