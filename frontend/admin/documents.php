<!-- Template Management -->
<section class="template-management">
    <h2>Template Management <i class="fas fa-file-download"></i></h2>
    <p>Upload Word document templates and set submission deadlines</p>
    <div class="template-controls">
        <button class="upload-btn"><i class="fas fa-upload"></i> Upload Template</button>
    </div>
    <div class="template-cards"></div>
</section>

<!-- Document Submissions -->
<section class="document-submission">
    <h2>Document Submission <i class="fas fa-upload"></i></h2>
    <p>Track and manage submitted documents by users.</p>
    <div class="submission-controls">
        <div class="search-bar">
            <input type="text" id="search-doc" placeholder="Search by ID or name...">
        </div>
        <div class="controls">
            <button class="download-selected-btn"><i class="fas fa-download"></i> Download Selected (0)</button>
            <button class="delete-selected-btn"><i class="fas fa-trash"></i> Delete Selected (0)</button>
        </div>
    </div>
    <div class="table">
        <table>
            <thead>
                <tr>
                    <th><input type="checkbox" class="record-checkbox" id="selectAll"></th>
                    <th>ID</th>
                    <th>Fullname</th>
                    <th>Department</th>
                    <th>Certificate</th>
                    <th>Registration</th>
                    <th>Status</th>
                    <th>Submission Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="submission-table-body"></tbody>
        </table>
    </div>
</section>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const templateContainer = document.querySelector(".template-cards");
    const uploadBtn = document.querySelector(".upload-btn");
    const submissionTableBody = document.getElementById("submission-table-body");
    const searchInput = document.getElementById("search-doc");


    // Select all checkboxes
    document.getElementById("selectAll").addEventListener("change", function() {
        const checkboxes = document.querySelectorAll(".record-checkbox");
        checkboxes.forEach(cb => cb.checked = this.checked);
    });

    // ================= Templates Section =================
    function loadTemplates() {
        fetch("../../backend/api/admindocument_api.php?action=list")
        .then(res => res.json())
        .then(data => {
            templateContainer.innerHTML = "";
            if (!data.length) {
                templateContainer.innerHTML = "<p>No templates found</p>";
                return;
            }
            data.forEach(doc => {
                const card = document.createElement("div");
                card.className = "template-card";
                card.innerHTML = `
                    <h3>${doc.title}</h3>
                    <p>${doc.purpose ?? doc.title + '.docx'}</p>
                    <p>Deadline: ${doc.deadline ?? '-'}</p>
                    <button class="download-btn" onclick="downloadTemplate('${doc.file_name}')"><i class="fas fa-download"></i> Download</button>
                    <button class="update-btn" onclick="deleteTemplate(${doc.id})"><i class="fas fa-trash"></i> Delete</button>
                `;
                templateContainer.appendChild(card);
            });
        });
    }

    /**uploadBtn.addEventListener("click", function() {
        const inputFile = document.createElement("input");
        inputFile.type = "file";
        inputFile.accept = ".doc,.docx";
        inputFile.onchange = function() {
            const file = inputFile.files[0];
            if (!file) return;
            const title = prompt("Enter template title:", file.name.replace(/\.[^/.]+$/, ""));
            const deadline = prompt("Enter deadline (YYYY-MM-DD):", "");
            const purpose = prompt("Purpose (optional):", "");
            const formData = new FormData();
            formData.append("title", title);
            formData.append("deadline", deadline);
            formData.append("purpose", purpose);
            formData.append("file", file);

            fetch("../../backend/api/admindocument_api.php?action=upload", {
                method: "POST",
                body: formData
            }).then(res => res.json())
            .then(resp => {
                if (resp.success) {
                    alert("Template uploaded successfully!");
                    loadTemplates();
                } else {
                    alert("Upload failed: " + (resp.error ?? ""));
                }
            });
        };
        inputFile.click();
    });*/

    window.downloadTemplate = function(fileName) {
        window.location.href = "../../backend/uploads/templates/" + fileName;
    }

    window.deleteTemplate = function(id) {
        if (!confirm("Delete this template?")) return;
        fetch(`../../backend/api/admindocument_api.php?action=delete&id=${id}`)
        .then(res => res.json())
        .then(resp => { if(resp.success) loadTemplates(); });
    }

    // ================= Submissions Section =================
    function loadSubmissions(query = "") {
        fetch("../../backend/api/document_api.php?action=list")
        .then(res => res.json())
        .then(data => {
            submissionTableBody.innerHTML = "";
            if (!data.length) {
                submissionTableBody.innerHTML = "<tr><td colspan='9'>No submissions found</td></tr>";
                return;
            }

            data.filter(doc => 
                String(doc.id).includes(query) || String(doc.user_id).includes(query)
            ).forEach(doc => {
                const row = document.createElement("tr");
                row.innerHTML = `
                    <td><input type="checkbox" class="record-checkbox" value="${doc.id}"></td>
                    <td>${submissionTableBody.children.length + 1}</td>
                    <td>${doc.full_name}</td>
                    <td>${doc.department ?? '-'}</td>
                    <td>${doc.certificate_status === 'uploaded' ? '<span class="status-data uploaded">Uploaded</span>' : '<span class="status-data missing">Missing</span>'}</td>
                    <td>${doc.registration_status === 'uploaded' ? '<span class="status-data uploaded">Uploaded</span>' : '<span class="status-data missing">Missing</span>'}</td>
                    <td>
                        ${
                            doc.certificate_status === 'uploaded' && doc.registration_status === 'uploaded'
                                ? '<span class="status-data uploaded">completed</span>'
                                : '<span class="status-data missing">missing</span>'
                        }
                    </td>
                    <td>${doc.last_uploaded ?? '-'}</td>
                    <td>
                        <div class="action-buttons">
                            <button class="action-btn rounded" onclick="downloadSubmission('${doc.file_name}')"><i class="fas fa-download"></i> Download</button>
                            <button class="action-btn delete rounded" onclick="deleteSubmission(${doc.id})"><i class="fas fa-trash"></i> Delete</button>
                        </div>
                    </td>
                `;
                submissionTableBody.appendChild(row);
            });
        });
    }

    searchInput.addEventListener("input", () => loadSubmissions(searchInput.value));

    window.downloadSubmission = function(fileName) {
        window.location.href = "../../backend/uploads/" + fileName;
    }

    window.deleteSubmission = function(document_id) {
        if (!confirm("Delete this submission?")) return;
        fetch(`../../backend/api/document_api.php?action=delete&id=${document_id}`)
        .then(res => res.json())
        .then(resp => { if(resp.success) loadSubmissions(searchInput.value); });
    }
    // Initial load
    loadTemplates();
    loadSubmissions();
});
</script>