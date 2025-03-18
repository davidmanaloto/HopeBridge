document.addEventListener("DOMContentLoaded", () => {
    loadVerificationRequests();
});

function loadVerificationRequests() {
    fetch("verify_fetch.php")
        .then(response => response.json())
        .then(requests => {
            const tableBody = document.getElementById("verification-table");
            tableBody.innerHTML = ""; // Clear previous data

            requests.forEach(request => {
                const row = document.createElement("tr");
                row.innerHTML = `
                    <td>${request.id}</td>
                    <td>${request.username}</td>
                    <td>${request.email}</td>
                    <td>${request.reason || "N/A"}</td>
                    <td>${request.document_path ? `<a href='uploads/${request.document_path}' target='_blank'>View</a>` : "No document"}</td>
                    <td class="status">${request.status}</td>
                    <td>
                        <button class="approve" onclick="updateVerification(${request.id}, 'Verified')">Approve</button>
                        <button class="reject" onclick="updateVerification(${request.id}, 'Rejected')">Reject</button>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        })
        .catch(error => console.error("Error fetching verification requests:", error));
}



function updateVerification(userId, status) {
    if (!confirm(`Are you sure you want to mark this user as ${status.toLowerCase()}?`)) return;

    fetch("verify_update.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `id=${userId}&status=${status}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(`User verification updated successfully.`);
            loadVerificationRequests(); // Refresh table dynamically
        } else {
            alert("Error updating verification.");
        }
    })
    .catch(error => console.error("Error:", error));
}
