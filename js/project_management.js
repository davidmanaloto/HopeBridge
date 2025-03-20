document.addEventListener("DOMContentLoaded", function () {
    loadProjects();
});

function loadProjects() {
    fetch("project_management.php")
        .then(response => response.json())
        .then(data => {
            const tableBody = document.getElementById("projectsTableBody");
            tableBody.innerHTML = ""; // Clear previous content

            data.forEach(project => {
                const row = document.createElement("tr");
                row.innerHTML = `
                    <td>${project.project_id}</td>
                    <td>${project.creator_name}</td>
                    <td>${project.project_name}</td>
                    <td>${project.organization_name || 'N/A'}</td>
                    <td>${project.donation_goal}</td>
                    <td>${project.funds_raised}</td>
                    <td>${project.created_at}</td>
                    <td>
                        <button class="delete-btn" data-id="${project.project_id}">Delete</button>
                    </td>
                `;
                tableBody.appendChild(row);
            });

            document.querySelectorAll(".delete-btn").forEach(button => {
                button.addEventListener("click", function () {
                    const projectId = this.getAttribute("data-id");
                    deleteProject(projectId);
                });
            });
        })
        .catch(error => console.error("Error loading projects:", error));
}

function deleteProject(projectId) {
    if (!confirm("Are you sure you want to delete this project?")) return;

    fetch("project_management.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({ project_id: projectId })
    })
    .then(response => response.json())
    .then(result => {
        alert(result.message);
        if (result.success) {
            loadProjects(); // Reload table after deletion
        }
    })
    .catch(error => console.error("Error deleting project:", error));
}
