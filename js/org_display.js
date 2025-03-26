document.addEventListener("DOMContentLoaded", () => {
    loadVerifiedOrganizations();
});

// Fetch and display verified organizations
function loadVerifiedOrganizations() {
    fetch("org_display.php?action=get_verified_orgs")
        .then(response => response.json())
        .then(data => {
            const tableBody = document.getElementById("orgTableBody");
            tableBody.innerHTML = "";

            if (data.length === 0) {
                tableBody.innerHTML = "<tr><td colspan='7'>No verified organizations</td></tr>";
                return;
            }
 
            data.forEach(org => {
                const row = document.createElement("tr");
                row.innerHTML = `
                    <td>${org.id}</td>
                    <td>${org.name}</td>
                    <td>${org.email}</td>
                    <td>${org.created_at}</td>
                    <td>${org.tags}</td>
                    <td>${org.description}</td>
                    <td>
                        <button class="edit-btn" onclick="openEditOrganizationModal(${org.id},'${org.name}','${org.tags}','${org.description}')">Edit</button>
                        <button class="delete-btn" onclick="deleteOrganization(${org.id})">Delete</button>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        })
        .catch(error => console.error("Error loading data:", error));
}
// Open the edit modal and populate fields
function openEditOrganizationModal(id, name, tags, description) {
    document.getElementById("editOrgId").value = id;
    document.getElementById("editOrgName").value = name;
    document.getElementById("editOrgTag").value = tags;
    document.getElementById("editOrgDescription").value = description;

    document.getElementById("editOrganizationModal").style.display = "block";
}

// Close the edit modal
function closeEditOrganizationModal() {
    document.getElementById("editOrganizationModal").style.display = "none";
}

// Save the edited organization details
function saveOrganizationChanges() {
    const id = document.getElementById("editOrgId").value;
    const name = document.getElementById("editOrgName").value;
    const tags = document.getElementById("editOrgTag").value;
    const description = document.getElementById("editOrgDescription").value;

    fetch("org_display.php?action=edit_organization", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `id=${encodeURIComponent(id)}&name=${encodeURIComponent(name)}&tags=${encodeURIComponent(tags)}&description=${encodeURIComponent(description)}`
    })
    .then(response => response.json())  
    .then(data => {
        if (data.success) {
            closeEditOrganizationModal();
            loadOrganizations();
        } else {
            alert("Failed to update organization: " + (data.error || "Unknown error"));
        }
    })
    .catch(error => console.error("Error updating organization:", error));
}

// Handle organization deletion
function deleteOrganization(id) {
    if (!confirm("Are you sure you want to delete this organization?")) return;

    let formData = new FormData();
    formData.append("action", "delete_org");
    formData.append("id", id);

    fetch("org_display.php?action=delete_org", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById(`org-row-${id}`).remove();
        } else {
            alert("Error: Unable to delete organization.");
        }
    })
    .catch(error => console.error("Request failed:", error));
}
