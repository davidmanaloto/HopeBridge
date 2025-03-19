document.addEventListener("DOMContentLoaded", function () {
    loadOrganizations();
});

function loadOrganizations() {
    fetch("org_action.php?action=get_organizations")
        .then(response => response.json())
        .then(organizations => {
            const tableBody = document.getElementById("organizationTableBody");
            tableBody.innerHTML = "";

            if (organizations.length === 0) {
                tableBody.innerHTML = `<tr><td colspan='7' style='text-align: center;'>No organizations found</td></tr>`;
                return;
            }

            organizations.forEach(org => {
                const row = document.createElement("tr");
                row.innerHTML = `
                    <td>${org.id}</td>
                    <td>${org.name}</td>
                    <td><a href="${org.website}" target="_blank">Visit</a></td>
                    <td>${org.donation_link ? `<a href="${org.donation_link}" target="_blank">Donate</a>` : "No Link"}</td>
                    <td>${org.tags}</td>
                    <td>${org.description}</td>
                    <td>
                         <button onclick="openEditOrganizationModal(${org.id}, '${org.name}', '${org.website}', '${org.donation_link}','${org.tags}','${org.description}')">Edit</button>
                        <button onclick="deleteOrganization(${org.id})">Delete</button>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        })
        .catch(error => console.error("Error fetching organizations:", error));
}
function showAddOrganizationModal() {
    // Clear previous input values
    document.getElementById("orgName").value = "";
    document.getElementById("orgWebsite").value = "";
    document.getElementById("orgDonationLink").value = "";
    document.getElementById("orgTags").value = "";
    document.getElementById("orgDescription").value = "";

    // Show the modal
    document.getElementById("addOrganizationModal").style.display = "block";
}

function closeAddOrganizationModal() {
    document.getElementById("addOrganizationModal").style.display = "none";
}

function addOrganization() {
    const name = document.getElementById("orgName").value.trim();
    const website = document.getElementById("orgWebsite").value.trim();
    const donationLink = document.getElementById("orgDonationLink").value.trim();
    const tags = document.getElementById("orgTags").value.trim();
    const description = document.getElementById("orgDescription").value.trim();

    // Prevent empty submissions
    if (!name || !website || !donationLink || !tags || !description) {
        alert("Please fill in all fields before submitting.");
        return;
    }

    fetch("org_action.php?action=add_organization", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `name=${encodeURIComponent(name)}&website=${encodeURIComponent(website)}&donation_link=${encodeURIComponent(donationLink)}&tags=${encodeURIComponent(tags)}&description=${encodeURIComponent(description)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadOrganizations();
            closeAddOrganizationModal();
        } else {
            alert("Failed to add organization.");
        }
    })
    .catch(error => console.error("Error adding organization:", error));
}


function openEditOrganizationModal(id, name, website, donationLink, tags, description) {
    document.getElementById("editOrgId").value = id;
    document.getElementById("editOrgName").value = name;
    document.getElementById("editOrgWebsite").value = website;
    document.getElementById("editOrgDonationLink").value = donationLink;
    document.getElementById("editOrgTag").value = tags;
    document.getElementById("editOrgDescription").value = description;

    document.getElementById("editOrganizationModal").style.display = "block";
}

function closeEditOrganizationModal() {
    document.getElementById("editOrganizationModal").style.display = "none";
}

function saveOrganizationChanges() {
    const id = document.getElementById("editOrgId").value;
    const name = document.getElementById("editOrgName").value;
    const website = document.getElementById("editOrgWebsite").value;
    const donationLink = document.getElementById("editOrgDonationLink").value;
    const tag = document.getElementById("editOrgTag").value;
    const description = document.getElementById("editOrgDescription").value;

    fetch("org_action.php?action=edit_organization", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `id=${encodeURIComponent(id)}&name=${encodeURIComponent(name)}&website=${encodeURIComponent(website)}&donation_link=${encodeURIComponent(donationLink)}&tags=${encodeURIComponent(tag)}&description=${encodeURIComponent(description)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeEditOrganizationModal();
            loadOrganizations();
        } else {
            alert("Failed to update organization.");
        }
    })
    .catch(error => console.error("Error updating organization:", error));
}

function deleteOrganization(id) {
    if (confirm("Are you sure you want to delete this organization?")) {
        fetch(`organization_action.php?action=delete_organization&id=${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadOrganizations();
                } else {
                    alert("Failed to delete organization.");
                }
            })
            .catch(error => console.error("Error deleting organization:", error));
    }
}
