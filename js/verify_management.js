document.addEventListener("DOMContentLoaded", () => {
    loadPendingOrganizations();
});

// Fetch and display pending organizations
function loadPendingOrganizations() {
    fetch("verify_management.php?action=get_orgs")
        .then(response => response.json())
        .then(data => {
            const tableBody = document.getElementById("verificationTableBody");
            tableBody.innerHTML = "";

            if (data.length === 0) {
                tableBody.innerHTML = "<tr><td colspan='6'>No pending requests</td></tr>";
                return;
            }

            data.forEach(org => {
                const row = document.createElement("tr");
                row.id = `org-row-${org.id}`;
                row.innerHTML = `
                    <td>${org.id}</td>
                    <td>${org.name}</td>
                    <td>${org.email}</td>
                    <td>${org.verification_status}</td>
                    <td>${org.created_at}</td>
                    <td>
                        <button onclick="handleVerification(${org.id}, 'approve')">Approve</button>
                        <button onclick="handleVerification(${org.id}, 'reject')">Reject</button>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        })
        .catch(error => console.error("Error loading data:", error));
}

// Handle verification actions
function handleVerification(id, action) {
    let formData = new FormData();
    formData.append("action", action + "_org");
    formData.append("id", id);

    if (action === "reject") {
        let reason = prompt("Enter rejection reason:");
        if (!reason) return;
        formData.append("reason", reason);
    }

    fetch("verify_management.php?action=" + action + "_org", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById(`org-row-${id}`).remove();
        } else {
            alert("Error: " + (data.error || "Action failed"));
        }
    })
    .catch(error => console.error("Request failed:", error));
}
