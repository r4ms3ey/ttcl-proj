<section class="template-management">
    <h2>Template Management <i class="fas fa-file-alt"></i></h2>
    <p>Manage templates used for attendance, notifications, or other system documents</p>

    <div class="controls-container">
        <div class="controls">
            <button class="add-btn" id="addTemplateBtn">
                <i class="fas fa-plus"></i> Add New Template
            </button>
        </div>
    </div>

    <div class="table">
        <table>
            <thead>
                <tr>
                    <th><span class="circle-icon" title="Select all"><i class="far fa-circle"></i></span></th>
                    <th>ID</th>
                    <th>Template Name</th>
                    <th>Type</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="templateTableBody">
                <!-- Data will be loaded dynamically from backend -->
            </tbody>
        </table>
        <p class="no-records" style="display: none;">No templates found for the selected criteria.</p>
    </div>
</section>

<script>
document.addEventListener("DOMContentLoaded", function() {
    fetchTemplates();
});

function fetchTemplates() {
    fetch("controllers/templatecontroller.php?action=view")
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById("templateTableBody");
            tbody.innerHTML = "";

            if (data.length === 0) {
                document.querySelector(".no-records").style.display = "block";
                return;
            }

            document.querySelector(".no-records").style.display = "none";

            data.forEach(template => {
                const tr = document.createElement("tr");
                tr.innerHTML = `
                    <td><span class="circle-icon" title="Select row"><i class="far fa-circle"></i></span></td>
                    <td>${template.id}</td>
                    <td>${template.name}</td>
                    <td>${template.type}</td>
                    <td>${template.created_at}</td>
                    <td>${template.updated_at}</td>
                    <td>
                        <div class="action-buttons">
                            <button class="action-btn edit" onclick="editTemplate(${template.id})"><i class="fas fa-edit"></i></button>
                            <button class="action-btn delete" onclick="deleteTemplate(${template.id})"><i class="fas fa-trash"></i></button>
                        </div>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        })
        .catch(err => console.error("Error fetching templates:", err));
}

function editTemplate(id) {
    alert("Edit template ID: " + id);
    // You can open a modal or redirect to edit page
}

function deleteTemplate(id) {
    if (!confirm("Are you sure you want to delete this template?")) return;

    fetch("controllers/templatecontroller.php?action=delete&id=" + id, {
        method: "GET"
    })
    .then(response => response.json())
    .then(res => {
        if (res.success) {
            alert("Template deleted successfully!");
            fetchTemplates();
        } else {
            alert("Failed to delete template.");
        }
    });
}
</script>
