document.addEventListener('DOMContentLoaded', function () {
    loadVerificationRequests();
});

// Load pending verification requests
function loadVerificationRequests() {
    fetch('verify_management.php?action=get_pending')
        .then(response => response.json())
        .then(requests => {
            const tableBody = document.getElementById("verificationTableBody");
            tableBody.innerHTML = "";

            if (requests.length === 0) {
                tableBody.innerHTML = "<tr><td colspan='9'>No pending verification requests</td></tr>";
                return;
            }

            requests.forEach(user => {
                const row = document.createElement("tr");
                row.innerHTML = `
                    <td>${user.id}</td>
                    <td>${user.username}</td>
                    <td>${user.email}</td>
                    <td>${user.status}</td>
                    <td>${user.verification_status}</td>
                    <td>${user.created_at}</td>
                    <td>${user.verification_reason}</td>
                    <td>
                        <a href="${user.verification_document}" target="_blank">View Document</a>
                    </td>
                    <td>
                        <button class="approve-btn" onclick="verifyUser(${user.id}, this)">Approve</button>
                        <button class="reject-btn" onclick="rejectUser(${user.id}, this)">Reject</button>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        })
        .catch(error => console.error("Error fetching verification requests:", error));
}

// Approve user verification
function verifyUser(id, button) {
    fetch('verify_management.php?action=verify_user&id=${userId}', {
        method: 'POST',
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `id=${id}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            button.closest("tr").remove();
        }
    })
    .catch(error => console.error("Error verifying user:", error));
}

// Reject user verification with reason
function rejectUser(id, button) {
    const reason = prompt("Enter a reason for rejection:");
    if (!reason) return;

    fetch('verify_management.php?action=reject_user&id=${userId}&reason=${encodeURIComponent(reason)}', {
        method: 'POST',
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `id=${id}&reason=${encodeURIComponent(reason)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            button.closest("tr").remove();
        }
    })
    .catch(error => console.error("Error rejecting user:", error));
}
